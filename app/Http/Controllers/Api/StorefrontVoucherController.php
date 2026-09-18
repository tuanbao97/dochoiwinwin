<?php

namespace App\Http\Controllers\Api;

use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\transaction\VoucherQuoteRequest;
use App\Support\StorefrontInventory;
use App\Support\StorefrontVoucher;
use App\Support\SapoVoucherSynchronizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorefrontVoucherController extends Controller
{
    public function __construct(
        private readonly StorefrontInventory $inventory,
        private readonly StorefrontVoucher $voucher,
        private readonly SapoVoucherSynchronizer $synchronizer
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'EMAIL' => ['nullable', 'email', 'max:1000'],
            'SO_DIEN_THOAI' => ['nullable', 'string', 'max:50'],
            'NHAN_TAI_CUA_HANG' => ['nullable', 'boolean'],
            'ITEMS' => ['required', 'array', 'min:1', 'max:50'],
            'ITEMS.*.PRODUCT_ID' => ['required', 'integer'],
            'ITEMS.*.QUANTITY' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $this->synchronizer->syncIfStale();
        $subtotal = $this->inventory->subtotalFromOrderItems($validated['ITEMS']);
        $pickupAtStore = filter_var($validated['NHAN_TAI_CUA_HANG'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $shippingFee = StorefrontVoucher::shippingFee($pickupAtStore);
        $vouchers = $this->voucher->availableList(
            $subtotal,
            $shippingFee,
            Auth::user()?->ID ? (int) Auth::user()->ID : null,
            $validated['EMAIL'] ?? null,
            $validated['SO_DIEN_THOAI'] ?? null
        );

        return response()->json(new ApiResponseDto(
            AppConstant::STATUS_SUCCESS,
            'Lấy danh sách mã giảm giá thành công.',
            ['VOUCHERS' => $vouchers],
            JsonResponse::HTTP_OK
        ))->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function quote(VoucherQuoteRequest $request): JsonResponse
    {
        $subtotal = $this->inventory->subtotalFromOrderItems($request->input('ITEMS', []));

        $user = Auth::user();
        $pickupAtStore = filter_var($request->input('NHAN_TAI_CUA_HANG', false), FILTER_VALIDATE_BOOLEAN);
        $shippingFee = StorefrontVoucher::shippingFee($pickupAtStore);
        $quote = $this->voucher->quoteMany(
            (array) $request->input('DISCOUNT_CODES', []),
            $subtotal,
            $shippingFee,
            $user?->ID ? (int) $user->ID : null,
            $request->input('EMAIL'),
            $request->input('SO_DIEN_THOAI')
        );

        return response()->json(new ApiResponseDto(
            AppConstant::STATUS_SUCCESS,
            'Áp dụng mã giảm giá thành công.',
            ['VOUCHER' => $quote],
            JsonResponse::HTTP_OK
        ))->setStatusCode(JsonResponse::HTTP_OK);
    }
}
