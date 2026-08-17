<?php

namespace App\Console\Commands;

use App\Enum\AppConstant;
use App\Models\DiscountVoucher;
use App\Service\SapoService;
use App\Support\StorefrontVoucher;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class CreateVoucherCommand extends Command
{
    protected $signature = 'voucher:create
        {code : Mã khách nhập, ví dụ WINWIN10}
        {--type=percentage : percentage|fixed_amount|free_shipping}
        {--value= : Phần trăm (vd 10 hoặc 10.25) hoặc số tiền VND}
        {--max-discount= : Mức giảm tối đa bằng VND}
        {--min-subtotal=0 : Giá trị đơn tối thiểu bằng VND}
        {--usage-limit= : Tổng lượt sử dụng tối đa}
        {--once-per-customer : Mỗi khách chỉ dùng một lần}
        {--starts-at= : ISO-8601, mặc định hiện tại}
        {--ends-at= : ISO-8601, bỏ trống nếu không hết hạn}
        {--combinable : Cho phép dùng chung với một mã khác trong cùng đơn}
        {--description= : Mô tả hiển thị cho khách}';

    protected $description = 'Tạo voucher local và đồng thời tạo PriceRule/DiscountCode trên Sapo';

    public function handle(SapoService $sapo): int
    {
        try {
            $code = StorefrontVoucher::normalizeCode((string) $this->argument('code'));
            if ($code === '' || mb_strlen($code) > 255) {
                throw new \InvalidArgumentException('Mã voucher phải có từ 1 đến 255 ký tự.');
            }
            if (DiscountVoucher::query()->where('CODE', $code)->exists()) {
                throw new \InvalidArgumentException('Mã '.$code.' đã tồn tại trong database.');
            }

            $type = strtoupper((string) $this->option('type'));
            $type = match ($type) {
                'PERCENTAGE' => StorefrontVoucher::TYPE_PERCENTAGE,
                'FIXED_AMOUNT' => StorefrontVoucher::TYPE_FIXED_AMOUNT,
                'FREE_SHIPPING' => StorefrontVoucher::TYPE_FREE_SHIPPING,
                default => throw new \InvalidArgumentException('type chỉ nhận percentage, fixed_amount hoặc free_shipping.'),
            };

            $value = $this->discountValue($type, $this->option('value'));
            $maximum = $this->nullableMoney($this->option('max-discount'), 'max-discount');
            $minimum = $this->money($this->option('min-subtotal'), 'min-subtotal');
            $usageLimit = $this->nullablePositiveInteger($this->option('usage-limit'), 'usage-limit');
            $startsAt = $this->date($this->option('starts-at')) ?? now();
            $endsAt = $this->date($this->option('ends-at'));
            if ($endsAt && ! $endsAt->greaterThan($startsAt)) {
                throw new \InvalidArgumentException('ends-at phải sau starts-at.');
            }

            if (! $sapo->isEnabled()) {
                throw new \RuntimeException('Sapo chưa được cấu hình đầy đủ; không tạo voucher nửa chừng ở local.');
            }

            $priceRulePayload = $this->priceRulePayload(
                $code,
                $type,
                $value,
                $maximum,
                $minimum,
                $usageLimit,
                (bool) $this->option('once-per-customer'),
                $startsAt,
                $endsAt
            );
            $ruleResponse = $sapo->post('/admin/price_rules.json', [
                'price_rule' => $priceRulePayload,
            ]);
            $rule = is_array($ruleResponse['price_rule'] ?? null)
                ? $ruleResponse['price_rule']
                : [];
            $ruleId = (int) ($rule['id'] ?? 0);
            if ($ruleId <= 0) {
                throw new \RuntimeException('Sapo không trả về PriceRule ID.');
            }

            $codeResponse = $sapo->post(
                '/admin/price_rules/'.$ruleId.'/discount_codes.json',
                ['discount_code' => ['code' => $code]]
            );
            $discountCode = is_array($codeResponse['discount_code'] ?? null)
                ? $codeResponse['discount_code']
                : [];
            $discountCodeId = (int) ($discountCode['id'] ?? 0);
            if ($discountCodeId <= 0) {
                throw new \RuntimeException(
                    'Sapo đã tạo PriceRule #'.$ruleId.' nhưng không tạo được DiscountCode; cần kiểm tra/xóa rule này trên Sapo.'
                );
            }

            $description = trim((string) $this->option('description'));
            if ($description === '') {
                $description = trim((string) ($rule['summary'] ?? ''));
            }

            $voucher = DiscountVoucher::query()->create([
                'SAPO_PRICE_RULE_ID' => $ruleId,
                'SAPO_DISCOUNT_CODE_ID' => $discountCodeId,
                'CODE' => $code,
                'TITLE' => $code,
                'DESCRIPTION' => $description !== '' ? $description : null,
                'DISCOUNT_TYPE' => $type,
                'DISCOUNT_VALUE' => $value,
                'MAX_DISCOUNT_AMOUNT' => $maximum,
                'MIN_SUBTOTAL' => $minimum,
                'USAGE_LIMIT' => $usageLimit,
                'ONCE_PER_CUSTOMER' => (bool) $this->option('once-per-customer'),
                'STARTS_AT' => $startsAt,
                'ENDS_AT' => $endsAt,
                'SAPO_PAYLOAD' => [
                    'price_rule' => $rule,
                    'discount_code' => $discountCode,
                ],
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true,
            ]);

            $this->info('Đã tạo voucher '.$voucher->CODE.' (local #'.$voucher->ID.', Sapo rule #'.$ruleId.').');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function discountValue(string $type, mixed $raw): int
    {
        if ($type === StorefrontVoucher::TYPE_FREE_SHIPPING) {
            return 0;
        }
        if ($raw === null || trim((string) $raw) === '') {
            throw new \InvalidArgumentException('Thiếu option --value.');
        }
        if ($type === StorefrontVoucher::TYPE_FIXED_AMOUNT) {
            $value = $this->money($raw, 'value');
            if ($value <= 0) {
                throw new \InvalidArgumentException('value phải lớn hơn 0.');
            }

            return $value;
        }

        $text = trim((string) $raw);
        if (! preg_match('/^(100(?:\.0{1,2})?|[0-9]{1,2}(?:\.[0-9]{1,2})?)$/', $text)) {
            throw new \InvalidArgumentException('Phần trăm phải từ 0.01 đến 100, tối đa 2 chữ số thập phân.');
        }
        [$whole, $decimal] = array_pad(explode('.', $text, 2), 2, '');
        $basisPoints = ((int) $whole * 100) + (int) str_pad($decimal, 2, '0');
        if ($basisPoints < 1 || $basisPoints > 10000) {
            throw new \InvalidArgumentException('Phần trăm phải từ 0.01 đến 100.');
        }

        return $basisPoints;
    }

    private function money(mixed $raw, string $name): int
    {
        $text = trim((string) $raw);
        if (! preg_match('/^\d+$/', $text)) {
            throw new \InvalidArgumentException($name.' phải là số nguyên VND không âm.');
        }

        return (int) $text;
    }

    private function nullableMoney(mixed $raw, string $name): ?int
    {
        return $raw === null || trim((string) $raw) === ''
            ? null
            : $this->money($raw, $name);
    }

    private function nullablePositiveInteger(mixed $raw, string $name): ?int
    {
        $value = $this->nullableMoney($raw, $name);
        if ($value !== null && $value < 1) {
            throw new \InvalidArgumentException($name.' phải lớn hơn 0.');
        }

        return $value;
    }

    private function date(mixed $raw): ?Carbon
    {
        return $raw === null || trim((string) $raw) === ''
            ? null
            : Carbon::parse((string) $raw)->setTimezone(config('app.timezone'));
    }

    /**
     * @return array<string, mixed>
     */
    private function priceRulePayload(
        string $code,
        string $type,
        int $value,
        ?int $maximum,
        int $minimum,
        ?int $usageLimit,
        bool $oncePerCustomer,
        Carbon $startsAt,
        ?Carbon $endsAt
    ): array {
        $freeShipping = $type === StorefrontVoucher::TYPE_FREE_SHIPPING;
        $percentage = $type === StorefrontVoucher::TYPE_PERCENTAGE;
        $payload = [
            'title' => $code,
            'target_type' => $freeShipping ? 'shipping_line' : 'line_item',
            'target_selection' => 'all',
            'allocation_method' => $freeShipping ? 'each' : 'across',
            'value_type' => $freeShipping || $percentage ? 'percentage' : 'fixed_amount',
            'value' => $freeShipping ? '-100' : ($percentage
                ? '-'.$this->formatPercentage($value)
                : '-'.$value),
            'customer_selection' => 'all',
            'once_per_customer' => $oncePerCustomer,
            'exclude_type' => true,
            'starts_on' => $startsAt->copy()->utc()->toIso8601String(),
            'combines_with' => [
                'order_discount' => (bool) $this->option('combinable'),
                'product_discount' => (bool) $this->option('combinable'),
                'shipping_discount' => (bool) $this->option('combinable'),
            ],
        ];
        if ($usageLimit !== null) {
            $payload['usage_limit'] = $usageLimit;
        }
        if ($maximum !== null && ! $freeShipping) {
            $payload['value_limit_amount'] = (string) $maximum;
        }
        if ($minimum > 0) {
            $payload['prerequisite_subtotal_range'] = [
                'greater_than_or_equal_to' => (string) $minimum,
            ];
        }
        if ($endsAt) {
            $payload['ends_on'] = $endsAt->copy()->utc()->toIso8601String();
        }

        return $payload;
    }

    private function formatPercentage(int $basisPoints): string
    {
        $whole = intdiv($basisPoints, 100);
        $fraction = $basisPoints % 100;

        return $fraction === 0
            ? (string) $whole
            : $whole.'.'.rtrim(str_pad((string) $fraction, 2, '0', STR_PAD_LEFT), '0');
    }
}
