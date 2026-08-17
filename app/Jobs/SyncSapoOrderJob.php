<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Service\SapoService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SyncSapoOrderJob
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $transactionId)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(SapoService $sapo): array
    {
        $lock = Cache::store('file')->lock('sapo-order-sync:'.$this->transactionId, 120);
        if (! $lock->get()) {
            return ['synced' => false, 'reason' => 'locked'];
        }

        try {
            $transaction = Transaction::query()
                ->with('orderItems')
                ->find($this->transactionId);

            if (! $transaction) {
                return ['synced' => false, 'reason' => 'not_found'];
            }

            if ((int) $transaction->SAPO_ORDER_ID > 0) {
                return [
                    'synced' => true,
                    'sapo_order_id' => (int) $transaction->SAPO_ORDER_ID,
                    'reason' => 'already_synced',
                ];
            }

            if (! $sapo->isEnabled()) {
                $transaction->SAPO_SYNC_STATUS = 'PENDING';
                $transaction->SAPO_SYNC_ERROR = 'Sapo API chưa được cấu hình.';
                $transaction->save();

                return ['synced' => false, 'reason' => 'disabled'];
            }

            $payload = $this->payload($transaction);
            $attempts = (int) $transaction->SAPO_SYNC_ATTEMPTS;
            $transaction->SAPO_SYNC_STATUS = 'PROCESSING';
            $transaction->SAPO_SYNC_ATTEMPTS = $attempts + 1;
            $transaction->SAPO_SYNC_ERROR = null;
            $transaction->SAPO_PAYLOAD = $payload;
            $transaction->save();

            $order = $attempts > 0
                ? $this->findExistingOrder($sapo, $transaction->ID)
                : null;
            if (! $order) {
                $response = $sapo->post('/admin/orders.json', $payload);
                $order = $response['order'] ?? null;
            }

            if (! is_array($order) || (int) ($order['id'] ?? 0) <= 0) {
                throw new RuntimeException('Sapo không trả về mã đơn hàng sau khi tạo.');
            }

            $transaction->SAPO_ORDER_ID = (int) $order['id'];
            $transaction->SAPO_ORDER_NAME = trim((string) ($order['name'] ?? '')) ?: null;
            $transaction->SAPO_SYNC_STATUS = 'SYNCED';
            $transaction->SAPO_SYNC_ERROR = null;
            $transaction->SAPO_SYNCED_AT = now();
            $transaction->save();

            Log::info('Sapo order synced', [
                'transaction_id' => $transaction->ID,
                'sapo_order_id' => $transaction->SAPO_ORDER_ID,
                'sapo_order_name' => $transaction->SAPO_ORDER_NAME,
            ]);

            return [
                'synced' => true,
                'sapo_order_id' => (int) $transaction->SAPO_ORDER_ID,
                'sapo_order_name' => $transaction->SAPO_ORDER_NAME,
            ];
        } catch (Throwable $e) {
            $failed = [
                'SAPO_SYNC_STATUS' => 'FAILED',
                'SAPO_SYNC_ERROR' => mb_substr($e->getMessage(), 0, 65000),
                'UPD_DT' => now(),
            ];
            if (str_contains($e->getMessage(), 'SAPO_VARIANT_ID')) {
                $failed['SAPO_SYNC_ATTEMPTS'] = 5;
            }

            Transaction::query()->whereKey($this->transactionId)->update($failed);

            Log::error('Sapo order sync failed', [
                'transaction_id' => $this->transactionId,
                'message' => $e->getMessage(),
            ]);

            return ['synced' => false, 'reason' => 'failed', 'message' => $e->getMessage()];
        } finally {
            $lock->release();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Transaction $transaction): array
    {
        $lineItems = [];
        foreach ($transaction->orderItems as $item) {
            $variantId = (int) $item->SAPO_VARIANT_ID;
            if ($variantId <= 0) {
                throw new RuntimeException(
                    'Sản phẩm local #'.$item->PRODUCT_ID.' chưa có SAPO_VARIANT_ID.'
                );
            }

            $lineItems[] = [
                'variant_id' => $variantId,
                'quantity' => max(1, (int) $item->QUANTITY),
            ];
        }

        if ($lineItems === []) {
            throw new RuntimeException('Đơn hàng không có sản phẩm để gửi sang Sapo.');
        }

        $fullName = trim((string) $transaction->USER_BUY_FULLNAME);
        $phone = $this->normalizePhone((string) $transaction->USER_BUY_PHONE);
        $email = trim((string) $transaction->USER_BUY_EMAIL);
        $address1 = trim((string) $transaction->USER_BUY_ADDRESS);

        $address = array_filter([
            'first_name' => $fullName,
            'last_name' => '',
            'address1' => $address1,
            'phone' => $phone,
            'country' => 'Vietnam',
            'country_code' => 'VN',
        ], static fn ($value) => $value !== null && $value !== '');

        $customer = array_filter([
            'first_name' => $fullName,
            'last_name' => '',
            'email' => $email,
            'phone' => $phone,
        ], static fn ($value) => $value !== null && $value !== '');

        $note = trim((string) $transaction->USER_BUY_MESSAGE);
        $note = 'Đơn website Win Win #'.$transaction->ID.($note !== '' ? "\n".$note : '');

        $order = [
            'financial_status' => 'pending',
            'send_receipt' => false,
            'send_fulfillment_receipt' => false,
            'inventory_behaviour' => 'decrement_ignoring_policy',
            'note' => $note,
            'tags' => 'Đồ Chơi Win Win, Website',
            'line_items' => $lineItems,
            'customer' => $customer,
            'shipping_address' => $address,
            'billing_address' => $address,
            'note_attributes' => [
                ['name' => 'local_transaction_id', 'value' => (string) $transaction->ID],
                ['name' => 'order_source', 'value' => 'dochoiwinwin.vn'],
            ],
        ];

        // Sapo "Thông tin liên hệ" đọc email/phone từ order + customer.
        if ($email !== '') {
            $order['email'] = $email;
        }
        if ($phone !== '') {
            $order['phone'] = $phone;
        }

        return ['order' => $order];
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s.\-()]/', '', trim($phone)) ?: '';
        if ($phone === '') {
            return '';
        }

        if (str_starts_with($phone, '+84')) {
            return $phone;
        }

        if (str_starts_with($phone, '84') && strlen($phone) >= 11) {
            return '+'.$phone;
        }

        if (str_starts_with($phone, '0') && strlen($phone) >= 10) {
            return '+84'.substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Look up a recently-created order before POSTing. This prevents duplicates
     * when Sapo created an order but the previous HTTP response was interrupted.
     *
     * @return array<string, mixed>|null
     */
    private function findExistingOrder(SapoService $sapo, int $transactionId): ?array
    {
        // Sapo trả về mảng rỗng nếu truyền `status=any` hoặc `fields`, nên chỉ lọc theo limit.
        $response = $sapo->get('/admin/orders.json', ['limit' => 250]);

        foreach (($response['orders'] ?? []) as $order) {
            if (! is_array($order)) {
                continue;
            }

            foreach (($order['note_attributes'] ?? []) as $attribute) {
                if (
                    is_array($attribute)
                    && ($attribute['name'] ?? null) === 'local_transaction_id'
                    && (string) ($attribute['value'] ?? '') === (string) $transactionId
                ) {
                    return $order;
                }
            }
        }

        return null;
    }
}
