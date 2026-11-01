<?php

namespace App\Mapper;

use App\Dto\transaction\TransactionDetailDto;
use App\Enum\TransactionStatusEnum;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Support\StorefrontVoucher;
use Illuminate\Support\Collection;

class TransactionMapper
{
    public static function mapFromEntity(?Transaction $transaction, bool $includeItems = true): ?TransactionDetailDto
    {
        if ($transaction === null) {
            return null;
        }

        $dto = TransactionDetailDto::createEmpty();
        $dto->id = $transaction->ID;
        $dto->maDon = $transaction->SAPO_ORDER_NAME ?: '#'.$transaction->ID;
        $dto->hoTen = $transaction->USER_BUY_FULLNAME;
        $dto->soDienThoai = $transaction->USER_BUY_PHONE;
        $dto->email = $transaction->USER_BUY_EMAIL;
        $dto->diaChi = $transaction->USER_BUY_ADDRESS;
        $dto->ghiChu = $transaction->USER_BUY_MESSAGE;
        $dto->tongSoLuong = $transaction->TOTAL_QUANTITY;
        $settled = $transaction->relationLoaded('orderItems')
            ? app(StorefrontVoucher::class)->settleFromLines($transaction)
            : null;
        $dto->tongTien = $settled['total'] ?? $transaction->TOTAL_PRICE;
        $dto->subtotal = (float) ($settled['subtotal'] ?? $transaction->SUBTOTAL_PRICE);
        $dto->shippingFee = (float) ($settled['shipping'] ?? $transaction->SHIPPING_FEE);
        $dto->discountAmount = (float) ($settled['discount'] ?? $transaction->DISCOUNT_AMOUNT);
        $dto->vouchers = self::mapVouchers($transaction);
        $dto->trangThaiGiaoDich = $transaction->TRANSACTION_STATUS;
        $dto->trangThaiGiaoDichText = self::resolveTransactionStatusText($transaction->TRANSACTION_STATUS);
        $dto->phuongThucThanhToan = $transaction->PAYMENT_METHOD;
        $dto->ngayTao = $transaction->CRT_DT;
        $dto->nhanVien = $transaction->SAPO_ASSIGNEE_NAME;
        $dto->ngayHenGiao = $transaction->EXPECTED_DELIVERY_DATE
            ? $transaction->EXPECTED_DELIVERY_DATE->format('Y-m-d H:i:s')
            : null;
        $dto->lyDoHuy = $transaction->TRANSACTION_STATUS === TransactionStatusEnum::CANCELLED->value
            ? $transaction->SAPO_CANCEL_REASON
            : null;

        if ($includeItems && $transaction->relationLoaded('orderItems')) {
            $dto->danhSachSanPham = $transaction->orderItems
                ->map(fn (OrderItem $item) => self::mapOrderLine($item))
                ->values()
                ->all();
        }

        return $dto;
    }

    public static function mapListFromPaginator(Collection $transactions): Collection
    {
        return $transactions->map(function ($transaction) {
            return self::mapFromEntity($transaction, false);
        });
    }

    public static function mapOrderLine(OrderItem $orderItem): array
    {
        $quantity = (float) $orderItem->QUANTITY;
        $price = (float) $orderItem->PRICE;

        return [
            'PRODUCT_ID' => $orderItem->PRODUCT_ID,
            'TEN_SAN_PHAM' => $orderItem->ATTR1,
            'HINH_ANH' => $orderItem->ATTR2,
            'HANDLE' => $orderItem->ATTR3,
            'SO_LUONG' => $quantity,
            'DON_GIA' => $price,
            'THANH_TIEN' => $quantity * $price,
        ];
    }

    /**
     * @return array<int, array{CODE: string, AMOUNT: float}>
     */
    private static function mapVouchers(Transaction $transaction): array
    {
        $snapshot = is_array($transaction->DISCOUNT_SNAPSHOT) ? $transaction->DISCOUNT_SNAPSHOT : [];
        $parts = is_array($snapshot['vouchers'] ?? null) ? $snapshot['vouchers'] : [];
        $vouchers = [];

        foreach ($parts as $part) {
            if (! is_array($part) || (int) ($part['discount_amount'] ?? 0) <= 0) {
                continue;
            }
            $vouchers[] = [
                'CODE' => (string) ($part['code'] ?? ''),
                'AMOUNT' => (float) $part['discount_amount'],
            ];
        }

        if ($vouchers === [] && (float) $transaction->DISCOUNT_AMOUNT > 0) {
            $vouchers[] = [
                'CODE' => (string) $transaction->DISCOUNT_CODE,
                'AMOUNT' => (float) $transaction->DISCOUNT_AMOUNT,
            ];
        }

        return $vouchers;
    }

    private static function resolveTransactionStatusText(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $enum = TransactionStatusEnum::tryFrom($status);

        return $enum?->description() ?? $status;
    }
}
