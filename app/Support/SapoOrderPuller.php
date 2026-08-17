<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Enum\TransactionStatusEnum;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Transaction;
use App\Service\SapoService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class SapoOrderPuller
{
    public const LAST_FETCH_SETTING_CODE = 'SETTING_SAPO_ORDERS_FETCHED_AT';

    private const PAGE_SIZE = 250;

    private const INITIAL_LOOKBACK_DAYS = 30;

    private const WINDOW_OVERLAP_SECONDS = 5;

    private const MIN_FETCH_INTERVAL_SECONDS = 25;

    public function __construct(private readonly SapoService $sapo)
    {
    }

    /**
     * Kéo toàn bộ đơn Sapo thay đổi từ mốc setting chung đến hiện tại.
     *
     * @return array{fetched: bool, updated: int, from: string|null, to: string|null, reason?: string}
     */
    public function pull(bool $force = false): array
    {
        if (! $this->sapo->isEnabled()) {
            return $this->result(false, reason: 'disabled');
        }

        $lock = Cache::store('file')->lock('sapo-orders-pull-global', 25);
        if (! $lock->get()) {
            return $this->result(false, reason: 'locked');
        }

        try {
            $setting = $this->setting();
            $lastFetchedAt = $this->parseSettingDate($setting->VALUE);

            if (
                ! $force
                && $lastFetchedAt
                && $lastFetchedAt->greaterThan(now()->subSeconds(self::MIN_FETCH_INTERVAL_SECONDS))
            ) {
                return $this->result(
                    false,
                    from: $lastFetchedAt->copy()->utc()->toIso8601String(),
                    reason: 'fresh'
                );
            }

            $localOrders = $this->localOrdersBySapoId(Transaction::query());

            $fetchUntil = now();
            $fetchFrom = $this->resolveFetchFrom($lastFetchedAt, $localOrders->min('CRT_DT'), $fetchUntil);
            $updated = $this->applyAll($this->fetchWindow($fetchFrom, $fetchUntil), $localOrders);

            // Chỉ tiến mốc sau khi đã đọc trọn cửa sổ API, tránh bỏ sót khi request lỗi.
            Setting::query()->whereKey(self::LAST_FETCH_SETTING_CODE)->update([
                'VALUE' => $fetchUntil->copy()->utc()->toIso8601String(),
                'UPD_DT' => now(),
                'UPD_NAME' => 'SYSTEM',
            ]);

            return $this->result(
                true,
                $updated,
                $fetchFrom->copy()->utc()->toIso8601String(),
                $fetchUntil->copy()->utc()->toIso8601String()
            );
        } catch (Throwable $e) {
            Log::warning('Unable to pull Sapo order updates', ['message' => $e->getMessage()]);

            return $this->result(false, reason: 'failed');
        } finally {
            $lock->release();
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Transaction>  $query
     * @return \Illuminate\Support\Collection<string, Transaction>
     */
    private function localOrdersBySapoId($query)
    {
        return $query
            ->where('STATUS', AppConstant::STATUS_USING)
            ->whereNotNull('SAPO_ORDER_ID')
            ->get()
            ->keyBy(fn (Transaction $order): string => (string) $order->SAPO_ORDER_ID);
    }

    /**
     * @param  array<int, array<string, mixed>>  $sapoOrders
     * @param  \Illuminate\Support\Collection<string, Transaction>  $localOrders
     */
    private function applyAll(array $sapoOrders, $localOrders): int
    {
        $updated = 0;

        foreach ($sapoOrders as $sapoOrder) {
            $localOrder = $localOrders->get((string) ($sapoOrder['id'] ?? ''));
            if ($localOrder instanceof Transaction && $this->apply($localOrder, $sapoOrder)) {
                $updated++;
            }
        }

        return $updated;
    }

    private function setting(): Setting
    {
        return Setting::query()->firstOrCreate(
            ['CODE' => self::LAST_FETCH_SETTING_CODE],
            [
                'NAME' => 'Mốc đồng bộ đơn hàng Sapo gần nhất',
                'TYPE' => 'SETTING_SYSTEM',
                'VALUE' => null,
                'UNIT' => 'ISO-8601 UTC',
                'DESCRIPTION' => 'Mốc chung dùng để kéo các đơn Sapo thay đổi đến hiện tại.',
                'CRT_NAME' => 'SYSTEM',
                'UPD_NAME' => 'SYSTEM',
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
            ]
        );
    }

    private function parseSettingDate(mixed $value): ?Carbon
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->setTimezone(config('app.timezone'));
        } catch (Throwable) {
            return null;
        }
    }

    private function resolveFetchFrom(
        ?CarbonInterface $lastFetchedAt,
        mixed $firstLocalOrderAt,
        CarbonInterface $fetchUntil
    ): Carbon {
        if ($lastFetchedAt) {
            return Carbon::instance($lastFetchedAt)->subSeconds(self::WINDOW_OVERLAP_SECONDS);
        }

        $fallback = $fetchUntil->copy()->subDays(self::INITIAL_LOOKBACK_DAYS);
        if ($firstLocalOrderAt instanceof CarbonInterface && $firstLocalOrderAt->greaterThan($fallback)) {
            return Carbon::instance($firstLocalOrderAt)->subSeconds(self::WINDOW_OVERLAP_SECONDS);
        }

        return Carbon::instance($fallback);
    }

    /**
     * Luôn gửi ISO-8601 UTC để local (+07:00) và production dùng cùng một mốc.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchWindow(CarbonInterface $from, CarbonInterface $to): array
    {
        return $this->fetchPaginated([
            'modified_on_min' => $from->copy()->utc()->toIso8601String(),
            'modified_on_max' => $to->copy()->utc()->toIso8601String(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function fetchPaginated(array $filters): array
    {
        $orders = [];

        for ($page = 1; ; $page++) {
            $response = $this->sapo->get('/admin/orders.json', $filters + [
                'page' => $page,
                'limit' => self::PAGE_SIZE,
            ]);
            $batch = $response['orders'] ?? [];
            $batch = is_array($batch) ? $batch : [];

            foreach ($batch as $order) {
                if (is_array($order)) {
                    $orders[] = $order;
                }
            }

            if (count($batch) < self::PAGE_SIZE) {
                break;
            }
        }

        return $orders;
    }

    /**
     * @param  array<string, mixed>  $sapoOrder
     */
    private function apply(Transaction $transaction, array $sapoOrder): bool
    {
        return DB::transaction(function () use ($transaction, $sapoOrder): bool {
            $transaction = Transaction::query()
                ->whereKey($transaction->ID)
                ->lockForUpdate()
                ->firstOrFail();
            $changes = ['SAPO_SYNCED_AT' => now(), 'UPD_DT' => now()];
            $itemsChanged = $this->reconcileOrderItems($transaction, $sapoOrder, $changes);

            $orderName = trim((string) ($sapoOrder['name'] ?? ''));
            if ($orderName !== '' && $orderName !== (string) $transaction->SAPO_ORDER_NAME) {
                $changes['SAPO_ORDER_NAME'] = $orderName;
            }

            $assignee = is_array($sapoOrder['assignee'] ?? null)
                ? $sapoOrder['assignee']
                : [];
            $assigneeId = (int) ($sapoOrder['assignee_id'] ?? ($assignee['id'] ?? 0)) ?: null;
            $assigneeName = trim(implode(' ', array_filter([
                trim((string) ($assignee['first_name'] ?? '')),
                trim((string) ($assignee['last_name'] ?? '')),
            ])));
            $assigneeName = $assigneeName !== ''
                ? $assigneeName
                : (trim((string) ($assignee['name'] ?? '')) ?: null);

            if ($assigneeId !== $transaction->SAPO_ASSIGNEE_ID) {
                $changes['SAPO_ASSIGNEE_ID'] = $assigneeId;
            }
            if ($assigneeName !== $transaction->SAPO_ASSIGNEE_NAME) {
                $changes['SAPO_ASSIGNEE_NAME'] = $assigneeName;
            }

            $expectedDeliveryRaw = trim((string) ($sapoOrder['expected_delivery_date'] ?? ''));
            $expectedDelivery = $expectedDeliveryRaw !== ''
                ? Carbon::parse($expectedDeliveryRaw)->setTimezone(config('app.timezone'))
                : null;
            if (
                $expectedDelivery?->getTimestamp()
                !== $transaction->EXPECTED_DELIVERY_DATE?->getTimestamp()
            ) {
                $changes['EXPECTED_DELIVERY_DATE'] = $expectedDelivery;
            }

            $status = $this->mapStatus($sapoOrder);
            if ($status->value !== (string) $transaction->TRANSACTION_STATUS) {
                $changes['TRANSACTION_STATUS'] = $status->value;
            }

            $cancelReason = $status === TransactionStatusEnum::CANCELLED
                ? $this->cancellationReason($sapoOrder)
                : null;
            if ($cancelReason !== $transaction->SAPO_CANCEL_REASON) {
                $changes['SAPO_CANCEL_REASON'] = $cancelReason;
            }

            $paidOn = trim((string) ($sapoOrder['paid_on'] ?? ''));
            if ($paidOn !== '' && $transaction->PAYMENT_DATE === null) {
                $changes['PAYMENT_DATE'] = Carbon::parse($paidOn)->setTimezone(config('app.timezone'));
            }

            Transaction::query()->whereKey($transaction->ID)->update($changes);

            return $itemsChanged || count($changes) > 2;
        });
    }

    /**
     * Đồng bộ snapshot sản phẩm local với line_items hiện tại trên Sapo.
     *
     * Không làm gì nếu API không trả key line_items, tránh xóa nhầm khi Sapo
     * thay đổi fields của endpoint danh sách.
     *
     * @param  array<string, mixed>  $sapoOrder
     * @param  array<string, mixed>  $transactionChanges
     */
    private function reconcileOrderItems(
        Transaction $transaction,
        array $sapoOrder,
        array &$transactionChanges
    ): bool {
        if (! array_key_exists('line_items', $sapoOrder)) {
            return false;
        }

        if (! is_array($sapoOrder['line_items'])) {
            throw new RuntimeException('Sapo trả về line_items không hợp lệ cho đơn '.$transaction->ID.'.');
        }

        $lines = array_values(array_filter(
            $sapoOrder['line_items'],
            static fn ($line): bool => is_array($line)
        ));
        $variantIds = collect($lines)
            ->pluck('variant_id')
            ->map(static fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();
        $sapoProductIds = collect($lines)
            ->pluck('product_id')
            ->map(static fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $variants = ProductVariant::query()
            ->whereIn('SAPO_VARIANT_ID', $variantIds)
            ->get()
            ->keyBy(fn (ProductVariant $variant): string => (string) $variant->SAPO_VARIANT_ID);
        $productsBySapoId = Product::query()
            ->whereIn('SAPO_ID', $sapoProductIds)
            ->get()
            ->keyBy(fn (Product $product): string => (string) $product->SAPO_ID);
        $productsById = Product::query()
            ->whereIn('ID', $variants->pluck('PRODUCT_ID')->filter()->unique())
            ->get()
            ->keyBy(fn (Product $product): string => (string) $product->ID);
        $thumbnails = $this->productThumbnails(
            $productsBySapoId->pluck('ID')->merge($productsById->pluck('ID'))
        );

        $existingItems = OrderItem::query()
            ->where('TRANSACTION_ID', $transaction->ID)
            ->lockForUpdate()
            ->get();
        $existingByLineId = $existingItems
            ->whereNotNull('SAPO_LINE_ITEM_ID')
            ->keyBy(fn (OrderItem $item): string => (string) $item->SAPO_LINE_ITEM_ID);
        $matchedIds = [];
        $activeQuantity = 0.0;
        $calculatedSubtotal = 0.0;
        $changed = false;

        foreach ($lines as $line) {
            $lineId = (int) ($line['id'] ?? 0);
            if ($lineId <= 0) {
                throw new RuntimeException('Sapo thiếu line item ID cho đơn '.$transaction->ID.'.');
            }

            $variantId = (int) ($line['variant_id'] ?? 0);
            $sapoProductId = (int) ($line['product_id'] ?? 0);
            // current_quantity về 0 khi hủy đơn dù sản phẩm không bị xóa.
            // quantity mới là số lượng snapshot hiện tại của dòng hàng.
            $quantity = max(0.0, (float) ($line['quantity'] ?? 0));
            $isDeleted = filter_var($line['deleted'] ?? false, FILTER_VALIDATE_BOOLEAN)
                || $quantity <= 0;
            $item = $existingByLineId->get((string) $lineId);

            // Lần đồng bộ đầu: gắn Sapo line ID vào dòng local đã tạo trước đó.
            if (! $item instanceof OrderItem && $variantId > 0) {
                $item = $existingItems->first(fn (OrderItem $candidate): bool =>
                    $candidate->SAPO_LINE_ITEM_ID === null
                    && (int) $candidate->SAPO_VARIANT_ID === $variantId
                    && ! in_array((int) $candidate->ID, $matchedIds, true)
                );
            }

            if ($isDeleted) {
                if ($item instanceof OrderItem) {
                    $matchedIds[] = (int) $item->ID;
                    $changed = $this->deactivateOrderItem($item) || $changed;
                }
                continue;
            }

            $variant = $variants->get((string) $variantId);
            $product = $variant instanceof ProductVariant
                ? $productsById->get((string) $variant->PRODUCT_ID)
                : $productsBySapoId->get((string) $sapoProductId);
            $name = trim((string) ($line['name'] ?? $line['title'] ?? 'Sản phẩm'));
            $price = (float) (
                array_key_exists('discounted_unit_price', $line)
                    ? $line['discounted_unit_price']
                    : ($line['price'] ?? 0)
            );

            $item ??= new OrderItem();
            $attributes = [
                'TRANSACTION_ID' => $transaction->ID,
                'SAPO_LINE_ITEM_ID' => $lineId,
                'PRODUCT_ID' => $product?->ID,
                'SAPO_PRODUCT_ID' => $sapoProductId ?: null,
                'PRODUCT_VARIANT_ID' => $variant?->ID,
                'SAPO_VARIANT_ID' => $variantId ?: null,
                'QUANTITY' => $quantity,
                'PRICE' => $price,
                'ATTR1' => $name !== '' ? $name : ($product?->NAME ?? 'Sản phẩm'),
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
                'UPD_NAME' => 'SYSTEM SAPO',
            ];

            // Sapo không trả ảnh/handle của storefront: lấy lại từ catalog local,
            // và giữ nguyên snapshot lúc đặt hàng nếu không tra được.
            $image = $thumbnails->get((string) $product?->ID);
            if ($image !== null) {
                $attributes['ATTR2'] = $image;
            }
            if (blank($item->ATTR3) && $product) {
                $attributes['ATTR3'] = Str::slug((string) $product->NAME);
            }

            $item->fill($attributes);
            if (! $item->exists) {
                $item->CRT_NAME = 'SYSTEM SAPO';
            }
            if ($item->isDirty() || ! $item->exists) {
                $item->save();
                $changed = true;
            }

            $matchedIds[] = (int) $item->ID;
            $activeQuantity += $quantity;
            $calculatedSubtotal += $quantity * $price;
        }

        foreach ($existingItems as $existingItem) {
            if (! in_array((int) $existingItem->ID, $matchedIds, true)) {
                $changed = $this->deactivateOrderItem($existingItem) || $changed;
            }
        }

        $isCancelled = $this->lower($sapoOrder['status'] ?? null) === 'cancelled'
            || ! empty($sapoOrder['cancelled_on']);
        $subtotal = $this->firstNumeric(
            $sapoOrder,
            $isCancelled
                ? ['subtotal_price', 'sub_total_price', 'total_line_items_price']
                : ['current_subtotal_price', 'subtotal_price', 'sub_total_price', 'total_line_items_price'],
            $calculatedSubtotal
        );
        $shippingFee = $this->firstNumeric(
            $sapoOrder,
            ['total_shipping_price'],
            $this->shippingLinesTotal($sapoOrder['shipping_lines'] ?? [])
        );
        $total = $this->firstNumeric(
            $sapoOrder,
            $isCancelled
                ? ['total_price', 'original_total_price']
                : ['current_total_price', 'total_price'],
            $subtotal + $shippingFee
        );
        $discount = $this->firstNumeric(
            $sapoOrder,
            ['total_discounts', 'current_total_discounts'],
            max(0, $subtotal + $shippingFee - $total)
        );

        $subtotalInt = max(0, (int) round($subtotal));
        $shippingInt = max(0, (int) round($shippingFee));
        $discountInt = max(0, (int) round($discount));
        $totalInt = max(0, (int) round($total));
        $lockedDiscount = $this->websiteLockedDiscount(
            $transaction,
            $subtotalInt,
            $shippingInt,
            $isCancelled
        );
        if ($lockedDiscount !== null) {
            $discountInt = $lockedDiscount;
            $totalInt = max(0, $subtotalInt + $shippingInt - $discountInt);
        }

        $transactionChanges['TOTAL_QUANTITY'] = $activeQuantity;
        $transactionChanges['SUBTOTAL_PRICE'] = $subtotalInt;
        $transactionChanges['SHIPPING_FEE'] = $shippingInt;
        $transactionChanges['DISCOUNT_AMOUNT'] = $discountInt;
        $transactionChanges['TOTAL_PRICE'] = $totalInt;

        foreach (['TOTAL_QUANTITY', 'SUBTOTAL_PRICE', 'SHIPPING_FEE', 'DISCOUNT_AMOUNT', 'TOTAL_PRICE'] as $field) {
            if ((float) $transaction->{$field} !== (float) $transactionChanges[$field]) {
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * Ảnh đại diện storefront: {DIRECTORY}/{ASPECT_RATIO}_{NAME}, khớp với
     * dữ liệu giỏ hàng gửi lên lúc đặt đơn.
     *
     * @param  \Illuminate\Support\Collection<int, mixed>  $productIds
     * @return \Illuminate\Support\Collection<string, string>
     */
    private function productThumbnails($productIds)
    {
        $productIds = $productIds->filter()->unique()->values();
        if ($productIds->isEmpty()) {
            return collect();
        }

        return DB::table('product_document_storage as pds')
            ->join('document_storage as ds', 'ds.ID', '=', 'pds.DOCUMENT_STORAGE_ID')
            ->whereIn('pds.PRODUCT_ID', $productIds)
            ->where('pds.ATTR1', 'DANH_SACH_HINH_ANH_DAI_DIEN')
            ->where('pds.STATUS', AppConstant::STATUS_USING)
            ->orderByDesc('pds.IS_THUMNAIL')
            ->orderBy('pds.SORT_ORDER')
            ->orderBy('pds.ID')
            ->select([
                'pds.PRODUCT_ID',
                'pds.ATTR2 as ASPECT_RATIO',
                'ds.NAME',
                'ds.DIRECTORY',
                'ds.PATH',
            ])
            ->get()
            ->groupBy(fn (object $row): string => (string) $row->PRODUCT_ID)
            ->map(function ($rows): ?string {
                $row = $rows->first();
                $directory = trim((string) $row->DIRECTORY, '/');
                $name = trim((string) $row->NAME);

                if ($directory !== '' && $name !== '') {
                    $aspectRatio = trim((string) ($row->ASPECT_RATIO ?? '')) ?: '1x1';

                    return $directory.'/'.$aspectRatio.'_'.$name;
                }

                return trim((string) $row->PATH) ?: null;
            })
            ->filter();
    }

    private function deactivateOrderItem(OrderItem $item): bool
    {
        if (
            $item->STATUS === AppConstant::STATUS_DELETED
            && ! (bool) $item->IS_ACTIVE
        ) {
            return false;
        }

        $item->STATUS = AppConstant::STATUS_DELETED;
        $item->IS_ACTIVE = false;
        $item->UPD_NAME = 'SYSTEM SAPO';
        $item->save();

        return true;
    }

    /**
     * @param  array<string, mixed>  $source
     * @param  array<int, string>  $keys
     */
    /**
     * Checkout website đã khóa số tiền giảm. Không để Sapo ghi đè bằng %
     * không có giới hạn tối đa, hoặc xóa giảm giá khi đơn bị hủy.
     */
    private function websiteLockedDiscount(
        Transaction $transaction,
        int $subtotal,
        int $shippingFee,
        bool $isCancelled
    ): ?int {
        $localAmount = (int) $transaction->DISCOUNT_AMOUNT;
        $snapshot = is_array($transaction->DISCOUNT_SNAPSHOT)
            ? $transaction->DISCOUNT_SNAPSHOT
            : [];
        $hasWebsiteVoucher = trim((string) $transaction->DISCOUNT_CODE) !== ''
            || $localAmount > 0
            || $snapshot !== [];

        if (! $hasWebsiteVoucher) {
            return null;
        }

        if ($isCancelled) {
            return max(0, $localAmount);
        }

        if ($snapshot !== []) {
            return app(StorefrontVoucher::class)->amountFromSnapshot(
                $snapshot,
                $subtotal,
                $shippingFee
            );
        }

        return max(0, $localAmount);
    }

    private function firstNumeric(array $source, array $keys, float $fallback): float
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $source) && is_numeric($source[$key])) {
                return (float) $source[$key];
            }
        }

        return $fallback;
    }

    private function shippingLinesTotal(mixed $shippingLines): float
    {
        if (! is_array($shippingLines)) {
            return 0.0;
        }

        return array_reduce(
            $shippingLines,
            static fn (float $total, mixed $line): float =>
                $total + (is_array($line) && is_numeric($line['price'] ?? null)
                    ? (float) $line['price']
                    : 0.0),
            0.0
        );
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function mapStatus(array $order): TransactionStatusEnum
    {
        $status = $this->lower($order['status'] ?? null);
        $fulfillment = $this->lower($order['fulfillment_status'] ?? null);
        $financial = $this->lower($order['financial_status'] ?? null);

        if ($status === 'cancelled' || ! empty($order['cancelled_on'])) {
            return TransactionStatusEnum::CANCELLED;
        }

        if (
            $status === 'closed'
            || $fulfillment === 'fulfilled'
            || ! empty($order['delivered_on'])
            || ! empty($order['completed_on'])
        ) {
            return TransactionStatusEnum::COMPLETED;
        }

        if (in_array($fulfillment, ['partial', 'packed', 'shipped', 'shipping'], true)) {
            return TransactionStatusEnum::SHIPPING;
        }

        if (! empty($order['confirmed_on']) || $financial === 'paid') {
            return TransactionStatusEnum::CONFIRMED;
        }

        return TransactionStatusEnum::PENDING;
    }

    /**
     * Sapo có thể trả lý do dạng mã, chuỗi tự nhập hoặc object tùy endpoint.
     *
     * @param  array<string, mixed>  $order
     */
    private function cancellationReason(array $order): ?string
    {
        $cancellation = is_array($order['cancellation'] ?? null)
            ? $order['cancellation']
            : [];
        $candidates = [
            $order['cancel_reason_description'] ?? null,
            $order['cancellation_reason'] ?? null,
            $order['cancel_reason'] ?? null,
            $order['cancel_note'] ?? null,
            $cancellation['description'] ?? null,
            $cancellation['reason'] ?? null,
            $cancellation['note'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (! is_scalar($candidate)) {
                continue;
            }

            $reason = trim((string) $candidate);
            if ($reason === '') {
                continue;
            }

            return match ($this->lower($reason)) {
                'customer' => 'Khách hàng yêu cầu hủy',
                'inventory' => 'Sản phẩm hết hàng',
                'fraud' => 'Đơn hàng có dấu hiệu gian lận',
                'declined' => 'Thanh toán bị từ chối',
                'other' => 'Lý do khác',
                default => $reason,
            };
        }

        return null;
    }

    /**
     * @return array{fetched: bool, updated: int, from: string|null, to: string|null, reason?: string}
     */
    private function result(
        bool $fetched,
        int $updated = 0,
        ?string $from = null,
        ?string $to = null,
        ?string $reason = null
    ): array {
        $result = compact('fetched', 'updated', 'from', 'to');
        if ($reason !== null) {
            $result['reason'] = $reason;
        }

        return $result;
    }

    private function lower(mixed $value): string
    {
        return mb_strtolower(trim((string) (is_scalar($value) ? $value : '')));
    }
}
