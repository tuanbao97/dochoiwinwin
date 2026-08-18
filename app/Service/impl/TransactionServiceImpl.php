<?php

namespace App\Service\impl;

use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use App\Enum\TransactionStatusEnum;
use App\Jobs\SyncSapoOrderJob;
use App\Mapper\TransactionMapper;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\User;
use App\Repository\TransactionRepository;
use App\Service\TransactionService;
use App\Support\StorefrontCart;
use App\Support\StorefrontIdentity;
use App\Support\StorefrontInventory;
use App\Support\StorefrontProfileWriter;
use App\Support\StorefrontVoucher;
use App\Utils\PaginationUtils;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransactionServiceImpl implements TransactionService
{
    private TransactionRepository $transactionRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        private readonly StorefrontInventory $inventory,
        private readonly StorefrontVoucher $voucher,
        private readonly StorefrontCart $cart,
    ) {
        $this->transactionRepository = $transactionRepository;
    }

    public function getListTransaction(Request $request)
    {
        $draw = $request->input('DRAW', 1);
        $page = (int) $request->query('PAGE', 1);
        $perPage = (int) $request->query('PER_PAGE', 10);
        $isGetAllElements = filter_var($request->query('IS_GET_ALL_ELEMENTS', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($isGetAllElements === true) {
            $perPage = 2147483647;
        }

        $tuKhoa = $request->input('TU_KHOA', null);
        $trangThaiGiaoDich = $request->input('TRANG_THAI_GIAO_DICH', null);

        $resultPagination = $this->transactionRepository->getList(
            $tuKhoa,
            $trangThaiGiaoDich,
            $page,
            $perPage
        );

        $listTransactionDto = TransactionMapper::mapListFromPaginator($resultPagination->getCollection());
        $resultPagination->setCollection($listTransactionDto);
        $customResponsePagination = PaginationUtils::pagination($resultPagination);
        $customResponsePagination['DRAW'] = $draw;

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Truy vấn thành công.',
                [
                    camelToSnakeUpper(class_basename(Transaction::class)) => $customResponsePagination,
                ],
                JsonResponse::HTTP_OK
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function getDetailTransaction($id, Request $request)
    {
        $transaction = $this->transactionRepository->getDetailWithItems((int) $id);
        $transactionDetail = null;
        if (! is_null($transaction)) {
            $transactionDetail = TransactionMapper::mapFromEntity($transaction, true);
        }

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Truy vấn thành công.',
                [
                    camelToSnakeUpper(class_basename(Transaction::class)) => $transactionDetail,
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function updateTransactionStatus($id, Request $request)
    {
        DB::beginTransaction();

        $transaction = $this->transactionRepository->getDetail((int) $id);
        $transaction->TRANSACTION_STATUS = $request->input('TRANSACTION_STATUS');
        $this->setAuditFields($transaction, false);
        $transaction->save();

        DB::commit();

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Cập nhật trạng thái thành công.',
                [
                    camelToSnakeUpper(class_basename(Transaction::class)) => TransactionMapper::mapFromEntity($transaction, false),
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function placeOrder(Request $request)
    {
        $requestedItems = $this->aggregateOrderItems($this->resolveOrderItems($request));
        $shippingFee = max(0, (int) config('storefront.shipping_fee', 30000));
        $discountCode = StorefrontVoucher::normalizeCode($request->input('DISCOUNT_CODE'));
        $discountCodes = StorefrontVoucher::normalizeCodes($request->input('DISCOUNT_CODES', $discountCode));

        $user = Auth::user();
        if (! $user instanceof User) {
            $user = app(StorefrontIdentity::class)->user();
        }
        $buyerName = $request->input('HO_TEN');
        $buyerPhone = $request->input('SO_DIEN_THOAI');
        $buyerEmail = $request->input('EMAIL');
        $buyerAddress = $request->input('DIA_CHI');
        $buyerNote = $request->input('GHI_CHU');

        try {
            $transaction = DB::transaction(function () use (
                $request,
                $requestedItems,
                $shippingFee,
                $discountCodes,
                $user,
                $buyerName,
                $buyerPhone,
                $buyerEmail,
                $buyerAddress,
                $buyerNote
            ): Transaction {
                $items = [];
                $totalQuantity = 0;
                $subtotal = 0;

                // Khóa theo ID tăng dần để các checkout đồng thời không thể
                // cùng mua vượt số lượng cuối cùng và giảm nguy cơ deadlock.
                ksort($requestedItems);
                foreach ($requestedItems as $requestedItem) {
                    $item = $this->resolveCatalogItem($requestedItem, true);
                    $quantity = (int) ($item['QUANTITY'] ?? 0);
                    $stock = (int) ($item['STOCK'] ?? 0);
                    if ($stock < $quantity) {
                        throw ValidationException::withMessages([
                            'ITEMS' => ($item['TEN_SAN_PHAM'] ?? 'Sản phẩm')
                                .' chỉ còn '.$stock.' sản phẩm trong kho.',
                        ]);
                    }

                    /** @var ProductVariant $variant */
                    $variant = $item['_VARIANT'];
                    $remaining = $stock - $quantity;
                    $variant->INVENTORY_QUANTITY = $remaining;
                    $variant->IS_IN_STOCK = $remaining > 0;
                    $variant->PRODUCT_STATUS = $remaining > 0 ? 'CON_HANG' : 'HET_HANG';
                    $variant->UPD_NAME = 'WEBSITE ORDER';
                    $variant->save();
                    Product::query()->whereKey($item['PRODUCT_ID'])->update([
                        'PRODUCT_QUANTITY' => DB::raw(
                            'GREATEST(COALESCE(PRODUCT_QUANTITY, 0) - '.(int) $quantity.', 0)'
                        ),
                        'UPD_DT' => now(),
                        'UPD_NAME' => 'WEBSITE ORDER',
                    ]);

                    unset($item['_VARIANT']);
                    $items[] = $item;
                    $totalQuantity += $quantity;
                    $subtotal += $quantity * (int) $item['PRICE'];
                }

                $discount = null;
                if ($discountCodes !== []) {
                    $discount = $this->voucher->redeemMany(
                        $discountCodes,
                        $subtotal,
                        $shippingFee,
                        $user?->ID ? (int) $user->ID : null,
                        $buyerEmail,
                        $buyerPhone
                    );
                }
                $discountAmount = (int) ($discount['discount_amount'] ?? 0);
                $totalPrice = max(0, $subtotal + $shippingFee - $discountAmount);

                $transaction = new Transaction();
                $transaction->USER_BUY_ID = $user?->ID;
                $transaction->USER_BUY_EMAIL = $buyerEmail;
                $transaction->USER_BUY_FULLNAME = $buyerName;
                $transaction->USER_BUY_PHONE = $buyerPhone;
                $transaction->USER_BUY_ADDRESS = $buyerAddress;
                $transaction->USER_BUY_MESSAGE = $buyerNote;
                $transaction->TOTAL_QUANTITY = $totalQuantity;
                $transaction->SUBTOTAL_PRICE = $subtotal;
                $transaction->TOTAL_PRICE = $totalPrice;
                $transaction->SHIPPING_FEE = $shippingFee;
                $transaction->DISCOUNT_VOUCHER_ID = $discount['voucher_id'] ?? null;
                $transaction->DISCOUNT_CODE = $discount['code'] ?? null;
                $transaction->DISCOUNT_AMOUNT = $discountAmount;
                $transaction->DISCOUNT_SNAPSHOT = $discount;
                $transaction->SHIPPING_VOUCHER_ID = $discount['extra_voucher_id'] ?? null;
                $transaction->SHIPPING_CODE = $discount['extra_code'] ?? null;
                $transaction->TRANSACTION_STATUS = TransactionStatusEnum::PENDING->value;
                $transaction->PAYMENT_METHOD = $request->input('PHUONG_THUC_THANH_TOAN');
                $transaction->SAPO_SYNC_STATUS = 'PENDING';
                $transaction->SAPO_SYNC_ATTEMPTS = 0;
                $transaction->STATUS = AppConstant::STATUS_USING;
                $transaction->IS_ACTIVE = true;
                $this->setAuditFields($transaction, true, $buyerName);
                $transaction->save();

                foreach ($items as $item) {
                    $orderItem = new OrderItem();
                    $orderItem->TRANSACTION_ID = $transaction->ID;
                    $orderItem->PRODUCT_ID = (int) ($item['PRODUCT_ID'] ?? 0);
                    $orderItem->PRODUCT_VARIANT_ID = $item['PRODUCT_VARIANT_ID'] ?? null;
                    $orderItem->SAPO_VARIANT_ID = $item['SAPO_VARIANT_ID'] ?? null;
                    $orderItem->QUANTITY = max(1, (int) ($item['QUANTITY'] ?? 0));
                    $orderItem->PRICE = (float) ($item['PRICE'] ?? 0);
                    $orderItem->ATTR1 = $item['TEN_SAN_PHAM'] ?? null;
                    $orderItem->ATTR2 = $item['HINH_ANH'] ?? null;
                    $orderItem->ATTR3 = $item['HANDLE'] ?? null;
                    $orderItem->STATUS = AppConstant::STATUS_USING;
                    $orderItem->IS_ACTIVE = true;
                    $this->setAuditFields($orderItem, true, $buyerName);
                    $orderItem->save();
                }

                $this->saveBuyerProfile($user, $buyerName, $buyerPhone, $buyerAddress);

                return $transaction;
            });
        } catch (Throwable $e) {
            Log::error('Storefront placeOrder failed', ['message' => $e->getMessage()]);
            throw $e;
        }

        try {
            $sapoSync = SyncSapoOrderJob::dispatchSync((int) $transaction->ID);
        } catch (Throwable $e) {
            $sapoSync = ['synced' => false];
            Transaction::query()->whereKey($transaction->ID)->update([
                'SAPO_SYNC_STATUS' => 'FAILED',
                'SAPO_SYNC_ERROR' => mb_substr($e->getMessage(), 0, 65000),
                'UPD_DT' => now(),
            ]);
            Log::error('Unable to start Sapo order sync', [
                'transaction_id' => $transaction->ID,
                'message' => $e->getMessage(),
            ]);
        }
        $transaction->refresh();

        if ($user instanceof User) {
            $this->cart->clear($user);
        }

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                'Đặt hàng thành công.',
                [
                    camelToSnakeUpper(class_basename(Transaction::class)) => [
                        'ID' => $transaction->ID,
                        'SAPO_ORDER_ID' => $transaction->SAPO_ORDER_ID,
                        'SAPO_ORDER_NAME' => $transaction->SAPO_ORDER_NAME,
                        'SAPO_SYNC_STATUS' => $transaction->SAPO_SYNC_STATUS,
                        'SAPO_SYNCED' => (bool) ($sapoSync['synced'] ?? false),
                        'SUBTOTAL' => (int) $transaction->SUBTOTAL_PRICE,
                        'SHIPPING_FEE' => (int) $transaction->SHIPPING_FEE,
                        'DISCOUNT_CODE' => $transaction->DISCOUNT_CODE,
                        'DISCOUNT_AMOUNT' => (int) $transaction->DISCOUNT_AMOUNT,
                        'TOTAL' => (int) $transaction->TOTAL_PRICE,
                    ],
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function resolveOrderItems(Request $request): array
    {
        $items = $request->input('ITEMS');
        if (is_array($items) && count($items) > 0) {
            return array_values($items);
        }

        return $this->cart->toOrderItems();
    }

    /**
     * Gộp các dòng cùng biến thể để không thể vượt tồn kho bằng cách gửi
     * nhiều item trùng ID trong payload checkout.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function aggregateOrderItems(array $items): array
    {
        $aggregated = [];

        foreach ($items as $item) {
            $resolved = $this->inventory->resolve((int) ($item['PRODUCT_ID'] ?? 0));
            if (! $resolved) {
                throw ValidationException::withMessages([
                    'ITEMS' => 'Sản phẩm trong giỏ không còn khả dụng.',
                ]);
            }

            $variantId = $resolved['variant_id'];
            $quantity = max(1, (int) ($item['QUANTITY'] ?? 0));
            if (! isset($aggregated[$variantId])) {
                $aggregated[$variantId] = $item;
                $aggregated[$variantId]['PRODUCT_ID'] = $variantId;
                $aggregated[$variantId]['QUANTITY'] = 0;
            }
            $aggregated[$variantId]['QUANTITY'] += $quantity;

            if ($aggregated[$variantId]['QUANTITY'] > $resolved['stock']) {
                throw ValidationException::withMessages([
                    'ITEMS' => $resolved['title'].' chỉ còn '.$resolved['stock'].' sản phẩm trong kho.',
                ]);
            }
        }

        return $aggregated;
    }

    /**
     * Product cards can submit a local variant ID, a local product ID, or
     * Sapo's default variant ID. Normalize all three before persisting.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function resolveCatalogItem(array $item, bool $lockForUpdate = false): array
    {
        $cartId = (int) ($item['PRODUCT_ID'] ?? 0);
        $resolved = $this->inventory->resolve($cartId, $lockForUpdate);
        if (! $resolved) {
            throw ValidationException::withMessages([
                'ITEMS' => 'Sản phẩm #'.$cartId.' không còn khả dụng.',
            ]);
        }

        $item['PRODUCT_ID'] = $resolved['product_id'];
        $item['PRODUCT_VARIANT_ID'] = $resolved['variant_id'];
        $item['SAPO_VARIANT_ID'] = $resolved['sapo_variant_id'];
        $item['PRICE'] = $resolved['price'];
        $item['TEN_SAN_PHAM'] = $resolved['title'];
        $item['HANDLE'] = $resolved['handle'];
        $item['STOCK'] = $resolved['stock'];
        $item['_VARIANT'] = $resolved['variant'];

        return $item;
    }

    private function saveBuyerProfile(?User $user, ?string $fullName, ?string $phone, ?string $address): void
    {
        if (! $user) {
            return;
        }

        StorefrontProfileWriter::save(
            (int) $user->ID,
            trim((string) $fullName) ?: (string) $user->FULL_NAME,
            (string) $user->EMAIL,
            (string) $phone,
            (string) $address
        );
    }

    private function setAuditFields(Transaction|OrderItem $model, bool $isCreate, ?string $buyerName = null): void
    {
        $user = Auth::user();
        $actorId = $user?->ID ?? AuthConstant::USER_SUPER_ADMIN_ID;
        $actorName = $user?->FULL_NAME ?? ($buyerName ?: AuthConstant::USER_SUPER_ADMIN_FULL_NAME);
        $now = Carbon::now();

        if ($isCreate) {
            $model->CRT_ID = $actorId;
            $model->CRT_NAME = $actorName;
            $model->CRT_DT = $now;
        }

        $model->UPD_ID = $actorId;
        $model->UPD_NAME = $actorName;
        $model->UPD_DT = $now;
    }
}
