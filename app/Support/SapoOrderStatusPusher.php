<?php

namespace App\Support;

use App\Enum\TransactionStatusEnum;
use App\Models\Transaction;
use App\Service\SapoService;
use RuntimeException;

/**
 * Đẩy trạng thái đơn local sang Sapo theo đúng API:
 * hủy, lưu trữ, mở lại, giao hàng và ghi nhận thanh toán là các endpoint riêng.
 * PUT /admin/orders/{id}.json chỉ sửa ghi chú, email, tag và note_attributes.
 */
class SapoOrderStatusPusher
{
    public function __construct(
        private readonly SapoService $sapo,
        private readonly SapoOrderPuller $orders,
    ) {
    }

    /**
     * @return bool true khi đơn đã có trên Sapo và trạng thái khớp sau khi gọi API
     */
    public function push(Transaction $transaction): bool
    {
        if (! $this->sapo->isEnabled() || (int) $transaction->SAPO_ORDER_ID <= 0) {
            return false;
        }

        $target = TransactionStatusEnum::tryFrom((string) $transaction->TRANSACTION_STATUS);
        if ($target === null) {
            return false;
        }

        $sapoId = (int) $transaction->SAPO_ORDER_ID;
        $order = $this->fetch($sapoId);
        if ($this->orders->statusOf($order) === $target) {
            return true;
        }

        match ($target) {
            TransactionStatusEnum::CANCELLED => $this->cancel($sapoId, $order),
            TransactionStatusEnum::COMPLETED => $this->complete($sapoId, $order),
            TransactionStatusEnum::CONFIRMED => $this->confirm($sapoId, $order),
            TransactionStatusEnum::SHIPPING => $this->ship($sapoId, $order),
            TransactionStatusEnum::PENDING => $this->pending($sapoId, $order),
        };

        return true;
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function cancel(int $sapoId, array $order): void
    {
        if ($this->isCancelled($order)) {
            return;
        }

        $this->sapo->post('/admin/orders/'.$sapoId.'/cancel.json', [
            'reason' => 'other',
            'email' => false,
            'restock' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function complete(int $sapoId, array $order): void
    {
        $this->guardNotCancelled($order, 'không chuyển sang hoàn thành được');
        if (! $this->isClosed($order)) {
            $this->sapo->post('/admin/orders/'.$sapoId.'/close.json');
        }
        $this->writeWebsiteStatus($sapoId, $order, null);
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function confirm(int $sapoId, array $order): void
    {
        $this->guardNotCancelled($order, 'không xác nhận được');
        $this->cancelFulfillments($sapoId, $order);
        $this->writeWebsiteStatus($sapoId, $order, null);
        if ($this->isClosed($order)) {
            $this->sapo->post('/admin/orders/'.$sapoId.'/open.json');
        }
        if (empty($order['confirmed_on']) && ! $this->isPaid($order)) {
            $this->recordPayment($sapoId, $order);
        }
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function ship(int $sapoId, array $order): void
    {
        $this->guardNotCancelled($order, 'không chuyển sang đang giao được');
        if ($this->isClosed($order)) {
            $this->sapo->post('/admin/orders/'.$sapoId.'/open.json');
        }

        // Ghi dấu trước khi tạo giao hàng. Sapo biến đơn một món thành fulfilled,
        // và lượt kéo sau vẫn phải đọc là đang giao cho đến khi lưu trữ đơn.
        $this->writeWebsiteStatus($sapoId, $order, TransactionStatusEnum::SHIPPING->value);

        $fulfillment = $this->lower($order['fulfillment_status'] ?? null);
        if (in_array($fulfillment, ['partial', 'packed', 'shipped', 'shipping', 'fulfilled'], true)) {
            return;
        }

        $this->sapo->post('/admin/orders/'.$sapoId.'/fulfillments.json', [
            'fulfillment' => [
                'send_notification_email' => false,
                'line_items' => $this->fulfillableLines($order),
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function pending(int $sapoId, array $order): void
    {
        if ($this->isCancelled($order)) {
            throw new RuntimeException('Sapo không mở lại đơn đã hủy.');
        }
        if ($this->isPaid($order) || ! empty($order['confirmed_on'])) {
            throw new RuntimeException('Sapo đã xác nhận đơn, không chuyển về chờ xác nhận được.');
        }

        $this->cancelFulfillments($sapoId, $order);
        $this->writeWebsiteStatus($sapoId, $order, null);
        if ($this->isClosed($order)) {
            $this->sapo->post('/admin/orders/'.$sapoId.'/open.json');
        }
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function recordPayment(int $sapoId, array $order): void
    {
        $amount = $this->amountDue($order);
        if ($amount <= 0) {
            return;
        }

        $financial = $this->lower($order['financial_status'] ?? null);
        $this->sapo->post('/admin/orders/'.$sapoId.'/transactions.json', [
            'transaction' => [
                'kind' => $financial === 'authorized' ? 'capture' : 'sale',
                'status' => 'success',
                'amount' => $amount,
                'currency' => trim((string) ($order['currency'] ?? '')) ?: 'VND',
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function cancelFulfillments(int $sapoId, array $order): void
    {
        foreach ($order['fulfillments'] ?? [] as $fulfillment) {
            if (! is_array($fulfillment)) {
                continue;
            }
            $id = (int) ($fulfillment['id'] ?? 0);
            $status = $this->lower($fulfillment['status'] ?? null);
            if ($id <= 0 || in_array($status, ['cancelled', 'canceled', 'failure'], true)) {
                continue;
            }
            $this->sapo->post('/admin/orders/'.$sapoId.'/fulfillments/'.$id.'/cancel.json');
        }
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function writeWebsiteStatus(int $sapoId, array $order, ?string $status): void
    {
        $attributes = [];
        foreach ($order['note_attributes'] ?? [] as $attribute) {
            if (! is_array($attribute) || ($attribute['name'] ?? null) === 'website_status') {
                continue;
            }
            $name = trim((string) ($attribute['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $attributes[] = [
                'name' => $name,
                'value' => (string) ($attribute['value'] ?? ''),
            ];
        }
        if ($status !== null && $status !== '') {
            $attributes[] = ['name' => 'website_status', 'value' => $status];
        }

        $this->sapo->put('/admin/orders/'.$sapoId.'.json', [
            'order' => [
                'id' => $sapoId,
                'note_attributes' => $attributes,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $order
     * @return array<int, array{id: int, quantity: int}>
     */
    private function fulfillableLines(array $order): array
    {
        $lines = [];
        foreach ($order['line_items'] ?? [] as $line) {
            if (! is_array($line)) {
                continue;
            }
            $id = (int) ($line['id'] ?? 0);
            $quantity = (int) ($line['fulfillable_quantity'] ?? $line['quantity'] ?? 0);
            if ($id <= 0 || $quantity <= 0) {
                continue;
            }
            $lines[] = ['id' => $id, 'quantity' => $quantity];
        }

        if ($lines === []) {
            throw new RuntimeException('Sapo không còn sản phẩm để chuyển sang đang giao.');
        }

        return $lines;
    }

    /**
     * @return array<string, mixed>
     */
    private function fetch(int $sapoId): array
    {
        $response = $this->sapo->get('/admin/orders/'.$sapoId.'.json');
        $order = $response['order'] ?? null;
        if (! is_array($order) || (int) ($order['id'] ?? 0) <= 0) {
            throw new RuntimeException('Sapo không trả về đơn hàng.');
        }

        return $order;
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function amountDue(array $order): float
    {
        if (is_numeric($order['total_outstanding'] ?? null)) {
            return max(0, (float) $order['total_outstanding']);
        }
        if ($this->isPaid($order)) {
            return 0;
        }

        return max(0, (float) ($order['total_price'] ?? 0));
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function guardNotCancelled(array $order, string $action): void
    {
        if ($this->isCancelled($order)) {
            throw new RuntimeException('Đơn Sapo đã hủy, '.$action.'.');
        }
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function isCancelled(array $order): bool
    {
        return in_array($this->lower($order['status'] ?? null), ['cancelled', 'canceled', 'cancel'], true)
            || ! empty($order['cancelled_on'])
            || ! empty($order['canceled_on']);
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function isClosed(array $order): bool
    {
        return $this->lower($order['status'] ?? null) === 'closed' || ! empty($order['closed_on']);
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function isPaid(array $order): bool
    {
        return in_array($this->lower($order['financial_status'] ?? null), [
            'paid',
            'partially_paid',
            'partially_refunded',
            'refunded',
        ], true);
    }

    private function lower(mixed $value): string
    {
        return strtolower(trim((string) $value));
    }
}
