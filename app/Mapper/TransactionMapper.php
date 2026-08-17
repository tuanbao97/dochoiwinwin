<?php

namespace App\Mapper;

use App\Dto\transaction\TransactionDetailDto;
use App\Enum\TransactionStatusEnum;
use App\Models\OrderItem;
use App\Models\Transaction;
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
        $dto->tongTien = $transaction->TOTAL_PRICE;
        $dto->trangThaiGiaoDich = $transaction->TRANSACTION_STATUS;
        $dto->trangThaiGiaoDichText = self::resolveTransactionStatusText($transaction->TRANSACTION_STATUS);
        $dto->phuongThucThanhToan = $transaction->PAYMENT_METHOD;
        $dto->ngayTao = $transaction->CRT_DT;

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

    private static function resolveTransactionStatusText(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $enum = TransactionStatusEnum::tryFrom($status);

        return $enum?->description() ?? $status;
    }
}
