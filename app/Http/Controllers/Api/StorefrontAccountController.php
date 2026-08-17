<?php

namespace App\Http\Controllers\Api;

use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Enum\TransactionStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Support\SapoOrderPuller;
use App\Support\StorefrontProfileWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorefrontAccountController extends Controller
{
    public function profile(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $social = $user->socialAccounts()
            ->orderByDesc('UPD_DT')
            ->first();
        $profile = $user->profile()->first();

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Truy vấn thành công.',
                [
                    'ID' => $user->ID,
                    'FULL_NAME' => $user->FULL_NAME ?: $user->USERNAME,
                    'EMAIL' => $user->EMAIL,
                    'PHONE' => $profile?->MOBILE,
                    'ADDRESS' => $profile?->ADDRESS,
                    'AVATAR_URL' => $social?->AVATAR_URL,
                    'PROVIDER' => $social?->PROVIDER,
                    'IS_STAFF' => $user->hasStaffAccess(),
                    'IS_ADMIN' => $user->isAdmin(),
                ],
                JsonResponse::HTTP_OK
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function updateProfile(Request $request)
    {
        $request->merge([
            'PHONE' => preg_replace('/[\s.\-]/', '', (string) $request->input('PHONE')),
        ]);

        $validated = $request->validate([
            'FULL_NAME' => ['required', 'string', 'max:500'],
            'PHONE' => [
                'required',
                'string',
                'max:50',
                'regex:/^(0|\+84)(3[2-9]|5[2689]|7[06-9]|8[0-9]|9[0-9])[0-9]{7}$/',
            ],
            'ADDRESS' => ['required', 'string', 'max:2000'],
        ]);

        /** @var User $user */
        $user = $request->user();
        DB::transaction(function () use ($user, $validated): void {
            StorefrontProfileWriter::save(
                (int) $user->ID,
                trim($validated['FULL_NAME']),
                (string) $user->EMAIL,
                $validated['PHONE'],
                $validated['ADDRESS']
            );
        });

        $user->FULL_NAME = trim($validated['FULL_NAME']);

        return $this->profile($request);
    }

    public function orders(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $this->claimGuestOrders($user);
        $perPage = min(max((int) $request->query('per_page', 10), 1), 20);

        $lastFetchedAt = Setting::query()
            ->whereKey(SapoOrderPuller::LAST_FETCH_SETTING_CODE)
            ->value('VALUE');

        $orders = $this->ownedOrdersQuery($user)
            ->with(['orderItems.product'])
            ->orderByDesc('CRT_DT')
            ->orderByDesc('ID')
            ->paginate($perPage);

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Truy vấn lịch sử mua hàng thành công.',
                [
                    'ITEMS' => collect($orders->items())
                        ->map(fn (Transaction $order): array => $this->serializeOrder($order))
                        ->values()
                        ->all(),
                    'CURRENT_PAGE' => $orders->currentPage(),
                    'LAST_PAGE' => $orders->lastPage(),
                    'PER_PAGE' => $orders->perPage(),
                    'TOTAL' => $orders->total(),
                    'SAPO_ORDERS_FETCHED_AT' => $lastFetchedAt,
                ],
                JsonResponse::HTTP_OK
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function orderDetail(Request $request, int $ID)
    {
        /** @var User $user */
        $user = $request->user();
        $this->claimGuestOrders($user);
        $order = $this->ownedOrdersQuery($user)
            ->with(['orderItems.product'])
            ->where('ID', $ID)
            ->firstOrFail();

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Truy vấn đơn hàng thành công.',
                $this->serializeOrder($order),
                JsonResponse::HTTP_OK
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    private function claimGuestOrders(User $user): void
    {
        $email = mb_strtolower(trim((string) $user->EMAIL));
        if ($email === '') {
            return;
        }

        Transaction::query()
            ->where('STATUS', AppConstant::STATUS_USING)
            ->whereNull('USER_BUY_ID')
            ->whereRaw('LOWER(USER_BUY_EMAIL) = ?', [$email])
            ->update([
                'USER_BUY_ID' => $user->ID,
                'UPD_DT' => now(),
                'UPD_ID' => $user->ID,
                'UPD_NAME' => $user->FULL_NAME ?: $user->EMAIL,
            ]);
    }

    private function ownedOrdersQuery(User $user)
    {
        $email = mb_strtolower(trim((string) $user->EMAIL));

        return Transaction::query()
            ->where('STATUS', AppConstant::STATUS_USING)
            ->whereRaw('LOWER(USER_BUY_EMAIL) = ?', [$email]);
    }

    private function serializeOrder(Transaction $order): array
    {
        $status = TransactionStatusEnum::tryFrom((string) $order->TRANSACTION_STATUS);

        return [
            'ID' => (int) $order->ID,
            'CODE' => $order->SAPO_ORDER_NAME ?: '#'.$order->ID,
            'STATUS' => $order->TRANSACTION_STATUS,
            'STATUS_LABEL' => $status?->description() ?? (string) $order->TRANSACTION_STATUS,
            'TOTAL_QUANTITY' => (float) $order->TOTAL_QUANTITY,
            'TOTAL_PRICE' => (float) $order->TOTAL_PRICE,
            'PAYMENT_METHOD' => $order->PAYMENT_METHOD,
            'CREATED_AT' => $order->CRT_DT?->toIso8601String(),
            'ASSIGNEE_NAME' => $order->SAPO_ASSIGNEE_NAME,
            'EXPECTED_DELIVERY_DATE' => $order->EXPECTED_DELIVERY_DATE?->toIso8601String(),
            'BUYER' => [
                'FULL_NAME' => $order->USER_BUY_FULLNAME,
                'EMAIL' => $order->USER_BUY_EMAIL,
                'PHONE' => $order->USER_BUY_PHONE,
                'ADDRESS' => $order->USER_BUY_ADDRESS,
                'MESSAGE' => $order->USER_BUY_MESSAGE,
            ],
            'ITEMS' => $order->orderItems->map(static fn ($item): array => [
                'ID' => (int) $item->ID,
                'PRODUCT_ID' => (int) $item->PRODUCT_ID,
                'NAME' => $item->ATTR1 ?: $item->product?->NAME,
                'IMAGE' => $item->ATTR2,
                'HANDLE' => $item->ATTR3,
                'QUANTITY' => (float) $item->QUANTITY,
                'PRICE' => (float) $item->PRICE,
                'LINE_TOTAL' => (float) $item->QUANTITY * (float) $item->PRICE,
            ])->values()->all(),
        ];
    }
}
