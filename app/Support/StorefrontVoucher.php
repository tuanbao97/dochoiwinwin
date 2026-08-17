<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Enum\TransactionStatusEnum;
use App\Models\DiscountVoucher;
use App\Models\Transaction;
use Illuminate\Validation\ValidationException;

class StorefrontVoucher
{
    public const TYPE_PERCENTAGE = 'PERCENTAGE';

    public const TYPE_FIXED_AMOUNT = 'FIXED_AMOUNT';

    public const TYPE_FREE_SHIPPING = 'FREE_SHIPPING';

    /**
     * Tính thử để hiển thị. Kết quả này không được dùng để lưu đơn hàng;
     * checkout phải gọi redeem() trong transaction để khóa lượt sử dụng.
     *
     * @return array<string, mixed>
     */
    public function quote(
        string $code,
        int $subtotal,
        int $shippingFee,
        ?int $userId = null,
        ?string $email = null,
        ?string $phone = null
    ): array {
        return $this->evaluateMany([$code], $subtotal, $shippingFee, $userId, $email, $phone, false);
    }

    /**
     * @param  array<int, string>  $codes
     * @return array<string, mixed>
     */
    public function quoteMany(
        array $codes,
        int $subtotal,
        int $shippingFee,
        ?int $userId = null,
        ?string $email = null,
        ?string $phone = null
    ): array {
        return $this->evaluateMany($codes, $subtotal, $shippingFee, $userId, $email, $phone, false);
    }

    /**
     * Phải gọi bên trong DB transaction.
     *
     * @return array<string, mixed>
     */
    public function redeem(
        string $code,
        int $subtotal,
        int $shippingFee,
        ?int $userId = null,
        ?string $email = null,
        ?string $phone = null
    ): array {
        return $this->evaluateMany([$code], $subtotal, $shippingFee, $userId, $email, $phone, true);
    }

    /**
     * @param  array<int, string>  $codes
     * @return array<string, mixed>
     */
    public function redeemMany(
        array $codes,
        int $subtotal,
        int $shippingFee,
        ?int $userId = null,
        ?string $email = null,
        ?string $phone = null
    ): array {
        return $this->evaluateMany($codes, $subtotal, $shippingFee, $userId, $email, $phone, true);
    }

    public static function normalizeCode(?string $code): string
    {
        return mb_strtoupper(trim((string) $code));
    }

    /**
     * @param  mixed  $codes
     * @return array<int, string>
     */
    public static function normalizeCodes(mixed $codes): array
    {
        if (! is_array($codes)) {
            $codes = preg_split('/[\s,;+]+/', (string) $codes) ?: [];
        }

        $normalized = [];
        foreach ($codes as $code) {
            $code = self::normalizeCode(is_scalar($code) ? (string) $code : '');
            if ($code !== '' && ! in_array($code, $normalized, true)) {
                $normalized[] = $code;
            }
        }

        return $normalized;
    }

    /**
     * Danh sách mã hiển thị trên checkout. Điều kiện và mô tả được đồng bộ từ Sapo.
     *
     * @return array<int, array<string, mixed>>
     */
    public function availableList(
        int $subtotal,
        int $shippingFee,
        ?int $userId = null,
        ?string $email = null,
        ?string $phone = null
    ): array {
        return DiscountVoucher::query()
            ->where('STATUS', AppConstant::STATUS_USING)
            ->where('IS_ACTIVE', true)
            ->orderByRaw('CASE WHEN MIN_SUBTOTAL <= ? THEN 0 ELSE 1 END', [max(0, $subtotal)])
            ->orderBy('ENDS_AT')
            ->orderBy('ID')
            ->get()
            ->map(function (DiscountVoucher $voucher) use (
                $subtotal,
                $shippingFee,
                $userId,
                $email,
                $phone
            ): array {
                $eligible = true;
                $message = 'Có thể áp dụng cho đơn hàng này.';
                $discountAmount = 0;

                try {
                    $quote = $this->quote(
                        (string) $voucher->CODE,
                        $subtotal,
                        $shippingFee,
                        $userId,
                        $email,
                        $phone
                    );
                    $discountAmount = (int) $quote['discount_amount'];
                } catch (ValidationException $e) {
                    $eligible = false;
                    $message = (string) (collect($e->errors())->flatten()->first()
                        ?: 'Chưa đủ điều kiện áp dụng.');
                }

                $payload = is_array($voucher->SAPO_PAYLOAD) ? $voucher->SAPO_PAYLOAD : [];
                $rule = is_array($payload['price_rule'] ?? null) ? $payload['price_rule'] : [];
                $codePayload = is_array($payload['discount_code'] ?? null)
                    ? $payload['discount_code']
                    : [];
                $timesUsed = max(
                    (int) ($rule['times_used'] ?? 0),
                    (int) ($codePayload['usage_count'] ?? 0)
                );
                $remaining = $voucher->USAGE_LIMIT !== null
                    ? max(0, (int) $voucher->USAGE_LIMIT - $timesUsed)
                    : null;

                $benefitParts = $this->benefitParts($voucher);

                return [
                    'code' => (string) $voucher->CODE,
                    'title' => (string) $voucher->TITLE,
                    'description' => $this->description($voucher),
                    'benefit' => $this->benefitLabel($voucher),
                    'benefit_headline' => $benefitParts['headline'],
                    'benefit_note' => $benefitParts['note'],
                    'type' => (string) $voucher->DISCOUNT_TYPE,
                    'discount_amount' => $discountAmount,
                    'min_subtotal' => (int) $voucher->MIN_SUBTOTAL,
                    'max_discount_amount' => $voucher->MAX_DISCOUNT_AMOUNT !== null
                        ? (int) $voucher->MAX_DISCOUNT_AMOUNT
                        : null,
                    'once_per_customer' => (bool) $voucher->ONCE_PER_CUSTOMER,
                    'starts_at' => $voucher->STARTS_AT?->toIso8601String(),
                    'ends_at' => $voucher->ENDS_AT?->toIso8601String(),
                    'remaining_uses' => $remaining,
                    'eligible' => $eligible,
                    'message' => $message,
                    'stackable' => $this->isStackable($voucher),
                    'source' => $voucher->SAPO_PRICE_RULE_ID ? 'SAPO' : 'LOCAL',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Tính lại số tiền giảm từ snapshot đã khóa lúc checkout.
     * Dùng khi Sapo kéo đơn và có thể tính % không giới hạn tối đa.
     *
     * @param  array<string, mixed>  $snapshot
     */
    public function amountFromSnapshot(array $snapshot, int $subtotal, int $shippingFee): int
    {
        $subtotal = max(0, $subtotal);
        $shippingFee = max(0, $shippingFee);
        $parts = is_array($snapshot['vouchers'] ?? null) ? $snapshot['vouchers'] : [];
        if ($parts === []) {
            $parts = [$snapshot];
        }

        $total = 0;
        foreach ($parts as $part) {
            if (! is_array($part)) {
                continue;
            }
            $total += $this->calculateDiscountAmount(
                strtoupper((string) ($part['type'] ?? '')),
                max(0, (int) ($part['value'] ?? 0)),
                isset($part['max_discount_amount']) && $part['max_discount_amount'] !== null
                    ? (int) $part['max_discount_amount']
                    : null,
                $subtotal,
                $shippingFee
            );
        }

        return min($total, $subtotal + $shippingFee);
    }

    /**
     * @param  array<int, string>  $codes
     * @return array<string, mixed>
     */
    private function evaluateMany(
        array $codes,
        int $subtotal,
        int $shippingFee,
        ?int $userId,
        ?string $email,
        ?string $phone,
        bool $lock
    ): array {
        $codes = self::normalizeCodes($codes);
        $subtotal = max(0, $subtotal);
        $shippingFee = max(0, $shippingFee);

        if ($codes === []) {
            throw $this->invalid('Vui lòng nhập mã giảm giá.');
        }
        if (count($codes) > 2) {
            throw $this->invalid('Mỗi đơn chỉ áp dụng tối đa 1 mã giảm giá và 1 mã freeship.');
        }

        $quotes = [];
        foreach ($codes as $code) {
            $quotes[] = $this->evaluateOne($code, $subtotal, $shippingFee, $userId, $email, $phone, $lock);
        }

        // Sapo quyết định mã nào được cộng dồn qua combines_with; website tôn trọng đúng cấu hình đó.
        // Một đơn chỉ nhận tối đa 1 mã thường, các mã còn lại bắt buộc phải là mã dùng chung được.
        $exclusive = array_values(array_filter(
            $quotes,
            static fn (array $quote): bool => ! $quote['stackable']
        ));
        if (count($exclusive) > 1) {
            throw $this->invalid(
                'Mã '.$exclusive[0]['code'].' và '.$exclusive[1]['code'].' không dùng chung được.'
            );
        }

        $discount = 0;
        foreach ($quotes as $quote) {
            $discount += (int) $quote['discount_amount'];
        }
        $discount = min($discount, $subtotal + $shippingFee);

        $primary = $quotes[0];
        $codesOrdered = array_map(static fn (array $quote): string => (string) $quote['code'], $quotes);

        return [
            'voucher_id' => (int) $primary['voucher_id'],
            'extra_voucher_id' => isset($quotes[1]) ? (int) $quotes[1]['voucher_id'] : null,
            'extra_code' => $quotes[1]['code'] ?? null,
            'code' => implode(', ', $codesOrdered),
            'codes' => $codesOrdered,
            'title' => implode(' + ', array_map(
                static fn (array $quote): string => (string) $quote['title'],
                $quotes
            )),
            'type' => count($quotes) > 1 ? 'STACKED' : (string) $primary['type'],
            'value' => (int) $primary['value'],
            'max_discount_amount' => $primary['max_discount_amount'],
            'min_subtotal' => (int) $primary['min_subtotal'],
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discount,
            'total' => max(0, $subtotal + $shippingFee - $discount),
            'vouchers' => $quotes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function evaluateOne(
        string $code,
        int $subtotal,
        int $shippingFee,
        ?int $userId,
        ?string $email,
        ?string $phone,
        bool $lock
    ): array {
        $code = self::normalizeCode($code);
        $subtotal = max(0, $subtotal);
        $shippingFee = max(0, $shippingFee);

        if ($code === '') {
            throw $this->invalid('Vui lòng nhập mã giảm giá.');
        }

        $query = DiscountVoucher::query()
            ->where('CODE', $code)
            ->where('STATUS', AppConstant::STATUS_USING)
            ->where('IS_ACTIVE', true);
        if ($lock) {
            $query->lockForUpdate();
        }

        /** @var DiscountVoucher|null $voucher */
        $voucher = $query->first();
        if (! $voucher) {
            throw $this->invalid('Mã giảm giá không tồn tại hoặc đã bị khóa.');
        }

        $now = now();
        if ($voucher->STARTS_AT && $voucher->STARTS_AT->isFuture()) {
            throw $this->invalid('Mã giảm giá chưa đến thời gian áp dụng.');
        }
        if ($voucher->ENDS_AT && ! $voucher->ENDS_AT->isFuture()) {
            throw $this->invalid('Mã giảm giá đã hết hạn.');
        }
        if ($subtotal < (int) $voucher->MIN_SUBTOTAL) {
            throw $this->invalid(
                'Đơn hàng phải đạt tối thiểu '
                .number_format((int) $voucher->MIN_SUBTOTAL, 0, ',', '.').' ₫ để dùng mã này.'
            );
        }

        $this->assertUsageAvailable($voucher, $userId, $email, $phone);

        $discount = $this->calculateDiscount($voucher, $subtotal, $shippingFee);
        if ($discount <= 0) {
            throw $this->invalid('Mã giảm giá không tạo ra giá trị giảm cho đơn hàng này.');
        }

        $type = strtoupper((string) $voucher->DISCOUNT_TYPE);

        return [
            'voucher_id' => (int) $voucher->ID,
            'code' => (string) $voucher->CODE,
            'title' => (string) $voucher->TITLE,
            'type' => $type,
            'value' => (int) $voucher->DISCOUNT_VALUE,
            'max_discount_amount' => $voucher->MAX_DISCOUNT_AMOUNT !== null
                ? (int) $voucher->MAX_DISCOUNT_AMOUNT
                : null,
            'min_subtotal' => (int) $voucher->MIN_SUBTOTAL,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discount,
            'stackable' => $this->isStackable($voucher),
            'total' => max(0, $subtotal + $shippingFee - $discount),
        ];
    }

    /**
     * Sapo đánh dấu mã có được cộng dồn hay không ở price_rule.combines_with.
     */
    private function isStackable(DiscountVoucher $voucher): bool
    {
        $payload = is_array($voucher->SAPO_PAYLOAD) ? $voucher->SAPO_PAYLOAD : [];
        $rule = is_array($payload['price_rule'] ?? null) ? $payload['price_rule'] : [];
        $combines = is_array($rule['combines_with'] ?? null) ? $rule['combines_with'] : [];

        return ! empty($combines['order_discount']) || ! empty($combines['product_discount']);
    }

    private function calculateDiscount(DiscountVoucher $voucher, int $subtotal, int $shippingFee): int
    {
        return $this->calculateDiscountAmount(
            strtoupper((string) $voucher->DISCOUNT_TYPE),
            max(0, (int) $voucher->DISCOUNT_VALUE),
            $voucher->MAX_DISCOUNT_AMOUNT !== null ? (int) $voucher->MAX_DISCOUNT_AMOUNT : null,
            $subtotal,
            $shippingFee
        );
    }

    private function calculateDiscountAmount(
        string $type,
        int $value,
        ?int $maximum,
        int $subtotal,
        int $shippingFee
    ): int {
        $discount = match ($type) {
            self::TYPE_PERCENTAGE => $this->percentageOf($subtotal, min($value, 10000)),
            self::TYPE_FIXED_AMOUNT => min($value, $subtotal),
            self::TYPE_FREE_SHIPPING => $shippingFee,
            default => 0,
        };

        if ($maximum !== null) {
            $discount = min($discount, max(0, $maximum));
        }

        return min($discount, $subtotal + $shippingFee);
    }

    /**
     * Tính floor(amount * basisPoints / 10000) mà không nhân hai số lớn
     * trực tiếp, tránh overflow số nguyên ở đơn hàng bất thường.
     */
    private function percentageOf(int $amount, int $basisPoints): int
    {
        return intdiv($amount, 10000) * $basisPoints
            + intdiv(($amount % 10000) * $basisPoints, 10000);
    }

    private function assertUsageAvailable(
        DiscountVoucher $voucher,
        ?int $userId,
        ?string $email,
        ?string $phone
    ): void {
        $usedOrders = Transaction::query()
            ->where('STATUS', AppConstant::STATUS_USING)
            ->where('TRANSACTION_STATUS', '!=', TransactionStatusEnum::CANCELLED->value)
            ->where(function ($query) use ($voucher): void {
                $id = (int) $voucher->ID;
                $query->where('DISCOUNT_VOUCHER_ID', $id)
                    ->orWhere('SHIPPING_VOUCHER_ID', $id);
            });

        if (
            $voucher->USAGE_LIMIT !== null
            && max(
                (clone $usedOrders)->count(),
                $this->sapoTimesUsed($voucher)
            ) >= (int) $voucher->USAGE_LIMIT
        ) {
            throw $this->invalid('Mã giảm giá đã hết lượt sử dụng.');
        }

        if (! $voucher->ONCE_PER_CUSTOMER) {
            return;
        }

        $email = mb_strtolower(trim((string) $email));
        $phone = preg_replace('/[\s.\-()]/', '', trim((string) $phone)) ?: '';
        if (! $userId && $email === '' && $phone === '') {
            throw $this->invalid('Vui lòng nhập email hoặc số điện thoại để kiểm tra lượt dùng mã.');
        }

        $customerOrders = clone $usedOrders;
        $customerOrders->where(function ($query) use ($userId, $email, $phone): void {
            $hasCondition = false;
            if ($userId) {
                $query->where('USER_BUY_ID', $userId);
                $hasCondition = true;
            }
            if ($email !== '') {
                $method = $hasCondition ? 'orWhereRaw' : 'whereRaw';
                $query->{$method}('LOWER(USER_BUY_EMAIL) = ?', [$email]);
                $hasCondition = true;
            }
            if ($phone !== '') {
                $method = $hasCondition ? 'orWhere' : 'where';
                $query->{$method}('USER_BUY_PHONE', $phone);
            }
        });

        if ($customerOrders->exists()) {
            throw $this->invalid('Bạn đã sử dụng mã giảm giá này trước đó.');
        }
    }

    private function invalid(string $message): ValidationException
    {
        return ValidationException::withMessages(['DISCOUNT_CODE' => $message]);
    }

    private function sapoTimesUsed(DiscountVoucher $voucher): int
    {
        $payload = is_array($voucher->SAPO_PAYLOAD) ? $voucher->SAPO_PAYLOAD : [];
        $rule = is_array($payload['price_rule'] ?? null) ? $payload['price_rule'] : [];
        $code = is_array($payload['discount_code'] ?? null) ? $payload['discount_code'] : [];

        return max((int) ($rule['times_used'] ?? 0), (int) ($code['usage_count'] ?? 0));
    }

    private function description(DiscountVoucher $voucher): string
    {
        $description = trim((string) ($voucher->DESCRIPTION ?? ''));
        if ($description !== '') {
            return $description;
        }

        $payload = is_array($voucher->SAPO_PAYLOAD) ? $voucher->SAPO_PAYLOAD : [];
        $rule = is_array($payload['price_rule'] ?? null) ? $payload['price_rule'] : [];
        $summary = trim((string) ($rule['summary'] ?? ''));

        return $summary !== '' ? $summary : $this->benefitLabel($voucher);
    }

    private function benefitLabel(DiscountVoucher $voucher): string
    {
        $type = strtoupper((string) $voucher->DISCOUNT_TYPE);
        if ($type === self::TYPE_FREE_SHIPPING) {
            return 'Miễn phí vận chuyển';
        }
        if ($type === self::TYPE_FIXED_AMOUNT) {
            return 'Giảm '.number_format((int) $voucher->DISCOUNT_VALUE, 0, ',', '.').' ₫';
        }

        $basisPoints = max(0, (int) $voucher->DISCOUNT_VALUE);
        $percent = number_format(
            $basisPoints / 100,
            $basisPoints % 100 === 0 ? 0 : 2,
            ',',
            ''
        );
        $label = 'Giảm '.$percent.'%';
        if ($voucher->MAX_DISCOUNT_AMOUNT !== null) {
            $label .= ' tối đa '
                .number_format((int) $voucher->MAX_DISCOUNT_AMOUNT, 0, ',', '.').' ₫';
        }

        return $label;
    }

    /**
     * Tách nhãn ưu đãi thành phần số lớn và ghi chú nhỏ để tem voucher dễ đọc.
     *
     * @return array{headline: string, note: string}
     */
    private function benefitParts(DiscountVoucher $voucher): array
    {
        $type = strtoupper((string) $voucher->DISCOUNT_TYPE);
        $stackNote = $this->isStackable($voucher) ? 'Dùng kèm mã khác' : '';

        if ($type === self::TYPE_FREE_SHIPPING) {
            return ['headline' => 'Freeship', 'note' => $stackNote];
        }

        if ($type === self::TYPE_FIXED_AMOUNT) {
            return [
                'headline' => number_format((int) $voucher->DISCOUNT_VALUE, 0, ',', '.').'₫',
                'note' => $stackNote !== '' ? $stackNote : 'Giảm trực tiếp',
            ];
        }

        $basisPoints = max(0, (int) $voucher->DISCOUNT_VALUE);
        $percent = number_format(
            $basisPoints / 100,
            $basisPoints % 100 === 0 ? 0 : 2,
            ',',
            ''
        );
        $note = $voucher->MAX_DISCOUNT_AMOUNT !== null
            ? 'Tối đa '.number_format((int) $voucher->MAX_DISCOUNT_AMOUNT, 0, ',', '.').'₫'
            : '';

        return [
            'headline' => 'Giảm '.$percent.'%',
            'note' => $note !== '' ? $note : $stackNote,
        ];
    }
}
