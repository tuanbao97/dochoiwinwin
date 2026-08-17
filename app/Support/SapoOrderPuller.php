<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Enum\TransactionStatusEnum;
use App\Models\Setting;
use App\Models\Transaction;
use App\Service\SapoService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        $changes = ['SAPO_SYNCED_AT' => now(), 'UPD_DT' => now()];

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

        $paidOn = trim((string) ($sapoOrder['paid_on'] ?? ''));
        if ($paidOn !== '' && $transaction->PAYMENT_DATE === null) {
            $changes['PAYMENT_DATE'] = Carbon::parse($paidOn)->setTimezone(config('app.timezone'));
        }

        Transaction::query()->whereKey($transaction->ID)->update($changes);

        return count($changes) > 2;
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
