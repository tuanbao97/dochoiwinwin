<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Models\DiscountVoucher;
use App\Service\SapoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SapoVoucherSynchronizer
{
    private const CACHE_KEY = 'sapo:vouchers:last-synced-at';

    public function __construct(private readonly SapoService $sapo)
    {
    }

    public function syncIfStale(int $seconds = 300): void
    {
        if (! $this->sapo->isEnabled()) {
            return;
        }

        $lastSyncedAt = (int) Cache::get(self::CACHE_KEY, 0);
        if ($lastSyncedAt > 0 && (time() - $lastSyncedAt) < $seconds) {
            return;
        }

        try {
            $this->sync();
        } catch (Throwable $e) {
            // Checkout vẫn dùng bản local gần nhất nếu Sapo tạm lỗi.
            Log::warning('Sapo voucher sync failed', ['message' => $e->getMessage()]);
        }
    }

    /**
     * @return array{rules: int, codes: int}
     */
    public function sync(): array
    {
        if (! $this->sapo->isEnabled()) {
            return ['rules' => 0, 'codes' => 0];
        }

        $ruleCount = 0;
        $codeCount = 0;
        $seenRuleIds = [];

        for ($page = 1; ; $page++) {
            $response = $this->sapo->get('/admin/price_rules.json', [
                'limit' => 250,
                'page' => $page,
            ]);
            $rules = is_array($response['price_rules'] ?? null)
                ? $response['price_rules']
                : [];

            foreach ($rules as $rule) {
                if (! is_array($rule) || (int) ($rule['id'] ?? 0) <= 0) {
                    continue;
                }

                $ruleCount++;
                $ruleId = (int) $rule['id'];
                $seenRuleIds[] = $ruleId;
                $codes = $this->discountCodes($ruleId);

                foreach ($codes as $codePayload) {
                    if (! is_array($codePayload)) {
                        continue;
                    }
                    $code = StorefrontVoucher::normalizeCode((string) ($codePayload['code'] ?? ''));
                    if ($code === '') {
                        continue;
                    }

                    $codeCount++;
                    $this->upsert($rule, $codePayload, $code);
                }
            }

            if (count($rules) < 250) {
                break;
            }
        }

        if ($seenRuleIds !== []) {
            DiscountVoucher::query()
                ->whereNotNull('SAPO_PRICE_RULE_ID')
                ->whereNotIn('SAPO_PRICE_RULE_ID', array_values(array_unique($seenRuleIds)))
                ->update(['IS_ACTIVE' => false]);
        }

        Cache::forever(self::CACHE_KEY, time());

        return ['rules' => $ruleCount, 'codes' => $codeCount];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function discountCodes(int $ruleId): array
    {
        $all = [];
        for ($page = 1; ; $page++) {
            $response = $this->sapo->get(
                '/admin/price_rules/'.$ruleId.'/discount_codes.json',
                ['limit' => 250, 'page' => $page]
            );
            $codes = is_array($response['discount_codes'] ?? null)
                ? $response['discount_codes']
                : [];
            $all = array_merge($all, $codes);
            if (count($codes) < 250) {
                break;
            }
        }

        return $all;
    }

    /**
     * @param  array<string, mixed>  $rule
     * @param  array<string, mixed>  $codePayload
     */
    private function upsert(array $rule, array $codePayload, string $code): void
    {
        $type = $this->type($rule);
        $value = $this->value($type, $rule['value'] ?? 0);
        $minimum = $this->minimumSubtotal($rule);
        $maximum = $this->nullableMoney($rule['value_limit_amount'] ?? null);
        $startsAt = $this->date($rule['starts_on'] ?? null) ?? now();
        $endsAt = $this->date($rule['ends_on'] ?? null);
        $active = strtolower((string) ($rule['status'] ?? 'active')) === 'active'
            && ! ($endsAt && $endsAt->isPast())
            && $this->isSupportedByStorefront($rule);

        DiscountVoucher::query()->updateOrCreate(
            ['CODE' => $code],
            [
                'SAPO_PRICE_RULE_ID' => (int) $rule['id'],
                'SAPO_DISCOUNT_CODE_ID' => (int) ($codePayload['id'] ?? 0) ?: null,
                'TITLE' => trim((string) ($rule['title'] ?? $code)) ?: $code,
                'DESCRIPTION' => $this->description($rule, $type, $value, $maximum, $minimum),
                'DISCOUNT_TYPE' => $type,
                'DISCOUNT_VALUE' => $value,
                'MAX_DISCOUNT_AMOUNT' => $maximum,
                'MIN_SUBTOTAL' => $minimum,
                'USAGE_LIMIT' => isset($rule['usage_limit']) && $rule['usage_limit'] !== null
                    ? max(0, (int) $rule['usage_limit'])
                    : null,
                'ONCE_PER_CUSTOMER' => (bool) ($rule['once_per_customer'] ?? false),
                'STARTS_AT' => $startsAt,
                'ENDS_AT' => $endsAt,
                'SAPO_PAYLOAD' => [
                    'price_rule' => $rule,
                    'discount_code' => $codePayload,
                    'synced_at' => now()->toIso8601String(),
                ],
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => $active,
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function type(array $rule): string
    {
        if (strtolower((string) ($rule['target_type'] ?? '')) === 'shipping_line') {
            return StorefrontVoucher::TYPE_FREE_SHIPPING;
        }

        return strtolower((string) ($rule['value_type'] ?? '')) === 'percentage'
            ? StorefrontVoucher::TYPE_PERCENTAGE
            : StorefrontVoucher::TYPE_FIXED_AMOUNT;
    }

    private function value(string $type, mixed $raw): int
    {
        if ($type === StorefrontVoucher::TYPE_FREE_SHIPPING) {
            return 0;
        }

        $absolute = ltrim(trim((string) $raw), '-');
        if ($type === StorefrontVoucher::TYPE_FIXED_AMOUNT) {
            return max(0, (int) round((float) $absolute));
        }

        if (! preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $absolute, $matches)) {
            return 0;
        }

        return min(10000, ((int) $matches[1] * 100)
            + (int) str_pad($matches[2] ?? '', 2, '0'));
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function minimumSubtotal(array $rule): int
    {
        foreach (['prerequisite_subtotal_range', 'prerequisite_sale_total_range'] as $key) {
            $range = $rule[$key] ?? null;
            if (is_array($range) && isset($range['greater_than_or_equal_to'])) {
                return max(0, (int) round((float) $range['greater_than_or_equal_to']));
            }
        }

        return 0;
    }

    private function nullableMoney(mixed $raw): ?int
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }

        return max(0, (int) round((float) $raw));
    }

    /**
     * Storefront hiện tính voucher trên toàn đơn. Không kích hoạt nhầm quy tắc
     * Sapo giới hạn theo khách/sản phẩm/bộ sưu tập vì sẽ làm sai số tiền.
     *
     * @param  array<string, mixed>  $rule
     */
    private function isSupportedByStorefront(array $rule): bool
    {
        if (strtolower((string) ($rule['customer_selection'] ?? 'all')) !== 'all') {
            return false;
        }
        if (strtolower((string) ($rule['target_selection'] ?? 'all')) !== 'all') {
            return false;
        }

        foreach ([
            'entitled_product_ids',
            'entitled_variant_ids',
            'entitled_collection_ids',
            'prerequisite_product_ids',
            'prerequisite_variant_ids',
            'prerequisite_collection_ids',
        ] as $key) {
            if (! empty($rule[$key])) {
                return false;
            }
        }

        return empty($rule['prerequisite_quantity_range']);
    }

    private function date(mixed $raw): ?Carbon
    {
        return $raw === null || trim((string) $raw) === ''
            ? null
            : Carbon::parse((string) $raw)->setTimezone(config('app.timezone'));
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function description(
        array $rule,
        string $type,
        int $value,
        ?int $maximum,
        int $minimum
    ): string {
        $summary = trim((string) ($rule['summary'] ?? ''));
        if ($summary !== '') {
            return $this->withCombineNote($rule, $summary);
        }

        $parts = [];
        if ($type === StorefrontVoucher::TYPE_FREE_SHIPPING) {
            $parts[] = 'Miễn phí vận chuyển';
        } elseif ($type === StorefrontVoucher::TYPE_FIXED_AMOUNT) {
            $parts[] = 'Giảm '.number_format($value, 0, ',', '.').' ₫';
        } else {
            $percent = number_format($value / 100, $value % 100 === 0 ? 0 : 2, ',', '');
            $parts[] = 'Giảm '.$percent.'%';
            if ($maximum !== null) {
                $parts[] = 'tối đa '.number_format($maximum, 0, ',', '.').' ₫';
            }
        }
        if ($minimum > 0) {
            $parts[] = 'cho đơn từ '.number_format($minimum, 0, ',', '.').' ₫';
        }

        return $this->withCombineNote($rule, implode(' ', $parts).'.');
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function withCombineNote(array $rule, string $description): string
    {
        $combines = is_array($rule['combines_with'] ?? null) ? $rule['combines_with'] : [];
        if (empty($combines['order_discount']) && empty($combines['product_discount'])) {
            return $description;
        }

        return rtrim($description, ' .').' • Dùng chung được với mã khác';
    }
}
