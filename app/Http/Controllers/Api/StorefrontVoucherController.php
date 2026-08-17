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
use Illuminate\Validation\ValidationException;

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
            'ITEMS' => ['required', 'array', 'min:1'],
            'ITEMS.*.PRODUCT_ID' => ['required', 'integer'],
            'ITEMS.*.QUANTITY' => ['required', 'integer', 'min:1'],
        ]);

        $this->synchronizer->syncIfStale();
        $subtotal = $this->subtotal($validated['ITEMS']);
        $vouchers = $this->voucher->availableList(
            $subtotal,
            max(0, (int) config('storefront.shipping_fee', 30000)),
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
        $subtotal = $this->subtotal($request->input('ITEMS', []));

        $user = Auth::user();
        $quote = $this->voucher->quoteMany(
            (array) $request->input('DISCOUNT_CODES', []),
            $subtotal,
            max(0, (int) config('storefront.shipping_fee', 30000)),
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

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function subtotal(array $items): int
    {
        $quantities = [];
        foreach ($items as $item) {
            $resolved = $this->inventory->resolve((int) ($item['PRODUCT_ID'] ?? 0));
            if (! $resolved) {
                throw ValidationException::withMessages([
                    'ITEMS' => 'Sản phẩm trong giỏ không còn khả dụng.',
                ]);
            }

            $variantId = (int) $resolved['variant_id'];
            $quantities[$variantId] = ($quantities[$variantId] ?? 0)
                + (int) ($item['QUANTITY'] ?? 0);
            if ($quantities[$variantId] > (int) $resolved['stock']) {
                throw ValidationException::withMessages([
                    'ITEMS' => $resolved['title'].' chỉ còn '.$resolved['stock'].' sản phẩm trong kho.',
                ]);
            }
        }

        $subtotal = 0;
        foreach ($quantities as $variantId => $quantity) {
            $resolved = $this->inventory->resolve($variantId);
            if (! $resolved) {
                throw ValidationException::withMessages([
                    'ITEMS' => 'Sản phẩm trong giỏ không còn khả dụng.',
                ]);
            }
            $subtotal += (int) $resolved['price'] * $quantity;
        }

        return $subtotal;
    }
}
