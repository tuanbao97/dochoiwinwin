<?php

namespace App\Http\Controllers\Api;

use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\AdminCustomers;
use App\Utils\PaginationUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $draw = $request->input('DRAW', 1);
        $page = max(1, (int) $request->query('PAGE', 1));
        $perPage = (int) $request->query('PER_PAGE', 10);
        $isGetAll = filter_var($request->query('IS_GET_ALL_ELEMENTS', false), FILTER_VALIDATE_BOOLEAN);
        if ($isGetAll || $perPage <= 0) {
            $perPage = 2147483647;
        }

        $rows = AdminCustomers::filter($this->rows(), $request->input('TU_KHOA'));
        $spend = array_sum(array_column($rows, 'spend'));
        $total = count($rows);
        $slice = array_slice($rows, ($page - 1) * $perPage, $perPage);
        $presented = array_map(
            static fn (array $row): array => AdminCustomers::present($row),
            $slice
        );

        $paginator = new LengthAwarePaginator($presented, $total, $perPage, $page);
        $payload = PaginationUtils::pagination($paginator);
        $payload['DRAW'] = $draw;
        $payload['TONG_CHI_TIEU'] = (int) $spend;

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Truy vấn thành công.',
                ['CUSTOMER' => $payload],
                JsonResponse::HTTP_OK
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function show(string $key)
    {
        $row = AdminCustomers::find($this->rows(), $key);
        if ($row === null) {
            return response()->json(
                new ApiResponseDto(false, 'Không tìm thấy khách hàng.', ['CUSTOMER' => null], JsonResponse::HTTP_NOT_FOUND)
            )->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Truy vấn thành công.',
                ['CUSTOMER' => AdminCustomers::present($row, true)],
                JsonResponse::HTTP_OK
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function rows(): array
    {
        $orders = Transaction::query()
            ->where('STATUS', AppConstant::STATUS_USING)
            ->orderByDesc('ID')
            ->get([
                'ID',
                'USER_BUY_ID',
                'USER_BUY_FULLNAME',
                'USER_BUY_PHONE',
                'USER_BUY_EMAIL',
                'USER_BUY_ADDRESS',
                'TOTAL_PRICE',
                'TOTAL_QUANTITY',
                'TRANSACTION_STATUS',
                'SAPO_ORDER_NAME',
                'CRT_DT',
            ])
            ->map(static function (Transaction $order): array {
                return [
                    'id' => (int) $order->ID,
                    'user_id' => (int) ($order->USER_BUY_ID ?? 0),
                    'name' => (string) ($order->USER_BUY_FULLNAME ?? ''),
                    'phone' => (string) ($order->USER_BUY_PHONE ?? ''),
                    'email' => (string) ($order->USER_BUY_EMAIL ?? ''),
                    'address' => (string) ($order->USER_BUY_ADDRESS ?? ''),
                    'total' => (int) $order->TOTAL_PRICE,
                    'quantity' => (int) $order->TOTAL_QUANTITY,
                    'status' => (string) $order->TRANSACTION_STATUS,
                    'code' => (string) ($order->SAPO_ORDER_NAME ?? ''),
                    'created_at' => $order->CRT_DT ? $order->CRT_DT->format('Y-m-d H:i:s') : '',
                ];
            })
            ->all();

        return AdminCustomers::summarize($orders, $this->accounts());
    }

    /**
     * @return list<array{id: int, name: string, phone: string, email: string, address: string, created_at: string}>
     */
    private function accounts(): array
    {
        return DB::table('user as u')
            ->join('title as t', function ($join): void {
                $join->on('t.USER_ID', '=', 'u.ID')
                    ->where('t.STATUS', AppConstant::STATUS_USING)
                    ->where('t.IS_ACTIVE', true);
            })
            ->join('role as r', function ($join): void {
                $join->on('r.ID', '=', 't.ROLE_ID')
                    ->where('r.CODE', 'USER')
                    ->where('r.STATUS', AppConstant::STATUS_USING)
                    ->where('r.IS_ACTIVE', true);
            })
            ->leftJoin('user_profile as p', function ($join): void {
                $join->on('p.USER_ID', '=', 'u.ID')
                    ->where('p.IS_DEFAULT', true)
                    ->where('p.STATUS', AppConstant::STATUS_USING);
            })
            ->where('u.STATUS', AppConstant::STATUS_USING)
            ->where('u.IS_ACTIVE', true)
            ->get([
                'u.ID',
                'u.FULL_NAME',
                'u.EMAIL',
                'u.CRT_DT',
                'p.MOBILE',
                'p.ADDRESS',
            ])
            ->map(static fn ($account): array => [
                'id' => (int) $account->ID,
                'name' => (string) ($account->FULL_NAME ?? ''),
                'phone' => (string) ($account->MOBILE ?? ''),
                'email' => (string) ($account->EMAIL ?? ''),
                'address' => (string) ($account->ADDRESS ?? ''),
                'created_at' => $account->CRT_DT ? (string) $account->CRT_DT : '',
            ])
            ->all();
    }
}
