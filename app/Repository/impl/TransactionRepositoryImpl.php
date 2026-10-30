<?php

namespace App\Repository\impl;

use App\Enum\AppConstant;
use App\Models\Transaction;
use App\Repository\BaseRepository;
use App\Repository\TransactionRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepositoryImpl extends BaseRepository implements TransactionRepository
{
    public function getModel()
    {
        return Transaction::class;
    }

    public function getDetail(int $id): ?Transaction
    {
        return Transaction::query()
            ->where([
                ['ID', '=', $id],
                ['STATUS', '=', AppConstant::STATUS_USING],
            ])
            ->first();
    }

    public function getDetailWithItems(int $id): ?Transaction
    {
        return Transaction::query()
            ->with(['orderItems'])
            ->where([
                ['ID', '=', $id],
                ['STATUS', '=', AppConstant::STATUS_USING],
            ])
            ->first();
    }

    public function getList(
        ?string $tuKhoa,
        ?string $trangThaiGiaoDich,
        int $page,
        int $perPage
    ): LengthAwarePaginator {
        $query = Transaction::query()
            ->with(['orderItems'])
            ->where('STATUS', AppConstant::STATUS_USING);

        if (! empty($tuKhoa)) {
            $query->where(function ($q) use ($tuKhoa) {
                $q->where('USER_BUY_FULLNAME', 'LIKE', '%' . $tuKhoa . '%')
                    ->orWhere('USER_BUY_PHONE', 'LIKE', '%' . $tuKhoa . '%')
                    ->orWhere('USER_BUY_EMAIL', 'LIKE', '%' . $tuKhoa . '%');

                if (is_numeric($tuKhoa)) {
                    $q->orWhere('ID', '=', (int) $tuKhoa);
                }
            });
        }

        if (! empty($trangThaiGiaoDich) && $trangThaiGiaoDich !== 'all') {
            $query->where('TRANSACTION_STATUS', $trangThaiGiaoDich);
        }

        return $query
            ->orderBy('CRT_DT', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
