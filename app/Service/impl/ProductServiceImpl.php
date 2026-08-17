<?php

namespace App\Service\impl;

use App\Dto\product\ProductBdsDatDetailDto;
use App\Dto\product\ProductDetailDto;
use App\Dto\productVariant\ProductVariantDetailDto;
use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Mapper\ProductMapper;
use App\Mapper\SapoMapper;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductDocumentStorageRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductVariantRepository;
use App\Service\ProductService;
use App\Service\SapoCatalogCache;
use App\Service\SapoService;
use App\Utils\PaginationUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductServiceImpl implements ProductService
{
    // Inject beans
    private ProductRepository $productRepository;
    private ProductDocumentStorageRepository $productDocumentStorageRepository;
    private ProductCategoryRepository $productCategoryRepository;
    private ProductVariantRepository $productVariantRepository;
    private SapoService $sapoService;
    private SapoCatalogCache $sapoCatalogCache;

    /**
     * Create a new class instance.
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductDocumentStorageRepository $productDocumentStorageRepository,
        ProductCategoryRepository $productCategoryRepository,
        ProductVariantRepository $productVariantRepository,
        SapoService $sapoService,
        SapoCatalogCache $sapoCatalogCache
    ) {
        $this->productRepository = $productRepository;
        $this->productDocumentStorageRepository = $productDocumentStorageRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->sapoService = $sapoService;
        $this->sapoCatalogCache = $sapoCatalogCache;
    }

    public function getOrNewSanPham($id): Product
    {
        $product = ($id != null) ? $this->productRepository->getDetailSanPham($id) : new Product();
        return $product;
    }

    public function getOrNewSanPhamWithFetchEdger($id): Product
    {
        $product = ($id != null) ? $this->productRepository->getDetailSanPhamWithFetchEdger($id) : new Product();
        return $product ?? new Product();
    }

    public function handleSaveFileDinhKem($productId,  array $documentStorages)
    {
        // Lưu document storages
        if (isset($documentStorages) && count($documentStorages) > 0) {
            $this->productDocumentStorageRepository->saveProductDocumentStorages($productId, $documentStorages);
        }
    }

    public function deleteAllSanPhamFileDinhKems($productId): bool
    {
        // Xóa document storage by product id
        return $this->productDocumentStorageRepository->deleteAllSanPhamFileDinhKems($productId);
    }

    public function handleSaveDanhMucSanPhams($productId,  array $categories)
    {
        // Xóa categories by product id
        self::deleteAllSanPhamDanhMucSanPham($productId);

        // Lưu categories
        if (isset($categories) && count($categories) > 0) {
            $this->productCategoryRepository->saveProductCategories($productId, $categories);
        }
    }

    public function deleteAllSanPhamDanhMucSanPham($productId): bool
    {
        // Xóa categories by product id
        return $this->productCategoryRepository->deleteAllSanPhamDanhMucSanPham($productId);
    }

    public function handleSaveBienTheSanPhams($productId,  array $productVariant)
    {
        // Xóa product variant by product id
        self::deleteAllBienTheSanPham($productId);

        // Lưu product variants
        if (isset($productVariant) && count($productVariant) > 0) {
            $this->productVariantRepository->saveBienTheSanPhams($productId, $productVariant);
        }
    }

    public function deleteAllBienTheSanPham($productId): bool 
    {
        // Xóa product variant by product id
        return $this->productVariantRepository->deleteAllBienTheSanPhamSanPham($productId);
    }

    public function saveSanPham(Request $request)
    {
        $id = $request->input('ID', null);

        // Bắt đầu một Transaction
        DB::beginTransaction();

        $data = $request->all();
        // Get hoặc new Product
        $product = self::getOrNewSanPham($id);
        // Mapper data từ request sang object
        ProductMapper::mapFromArray($product, $data);

        // Save
        $product->ATTR49 = AppConstant::TYPE_PRODUCT_COMMON;
        $product->ATTR50 = AppConstant::PATH_CHI_TIET_PRODUCT_COMMON;
        $arrProduct = $product->toArray();
        $product = $this->productRepository->save($arrProduct); // Lưu product vào database

        if ($product === null) {
            DB::rollBack();
            throw new \RuntimeException('Không lưu được sản phẩm.');
        }

        // Mặc định mã sản phẩm = ID nếu chưa nhập
        if (empty($product->MA_SAN_PHAM)) {
            $product->MA_SAN_PHAM = (string) $product->ID;
            $product = $this->productRepository->save($product->toArray());
        }
        
        // Nếu là update (có ID), fetch lại thông tin product
        if (!is_null($id)) {
            $product = $this->productRepository->getDetailSanPham((int) $id);
        }

        // Xử lý lưu thể loại danh mục sản phẩm
        $arrDanhMucSanPham = collect($request->input('DANH_MUC_SAN_PHAMS', []))
            ->pluck('ID')
            ->filter(fn ($categoryId) => is_numeric($categoryId))
            ->map(fn ($categoryId) => (int) $categoryId)
            ->unique()
            ->values()
            ->map(fn ($categoryId) => [
                'PRODUCT_ID' => $product->ID,
                'CATEGORY_ID' => $categoryId,
                'IS_ACTIVE' => true,
            ])
            ->all();
        // Lưu danh mục sản phẩm
        self::handleSaveDanhMucSanPhams($product->ID, $arrDanhMucSanPham);
        if ($request->exists('DANH_SACH_BIEN_THE')) {
            $this->productVariantRepository->saveBienTheSanPhams(
                $product->ID,
                $request->input('DANH_SACH_BIEN_THE', [])
            );
        }



        // Xóa document storage by product id
        self::deleteAllSanPhamFileDinhKems($product->ID);
        
        // Xử lý lưu danh sách hình ảnh đại diện upload vào database
        $danhSachHinhAnhDaiDienUpload = $request->input('DANH_SACH_HINH_ANH_DAI_DIEN', null);
        if (!is_null($danhSachHinhAnhDaiDienUpload) && count($danhSachHinhAnhDaiDienUpload) > 0) {
            // Lưu hình ảnh vào database
            self::handleSaveFileDinhKem($product->ID, $danhSachHinhAnhDaiDienUpload);
        }

        // Xử lý lưu danh sách hình ảnh upload vào database
        $danhSachHinhAnhUpload = $request->input('DANH_SACH_HINH_ANH', null);
        if (!is_null($danhSachHinhAnhUpload) && count($danhSachHinhAnhUpload) > 0) {
            // Lưu hình ảnh vào database
            self::handleSaveFileDinhKem($product->ID, $danhSachHinhAnhUpload);
        }

        // Xử lý lưu danh sách video upload vào database
        $danhSachVideoUpload = $request->input('DANH_SACH_VIDEO', null);
        if (!is_null($danhSachVideoUpload) && count($danhSachVideoUpload) > 0) {
            // Lưu video vào database
            self::handleSaveFileDinhKem($product->ID, $danhSachVideoUpload);
        }

        // Xử lý lưu danh sách file đính kèm upload vào database
        $danhSachFileDinhKemUpload = $request->input('DANH_SACH_FILE_DINH_KEM', null);
        if (!is_null($danhSachFileDinhKemUpload) && count($danhSachFileDinhKemUpload) > 0) {
            // Lưu video vào database
            self::handleSaveFileDinhKem($product->ID, $danhSachFileDinhKemUpload);
        }

        // Nếu mọi thứ thành công, commit Transaction
        DB::commit();

        return response()->json(
            new ApiResponseDto(
                AppConstant::STATUS_SUCCESS,
                is_null($id) ? 'Tạo mới thành công.' : 'Cập nhật thành công.',
                [
                    camelToSnakeUpper(class_basename(Product::class))
                        => new ProductDetailDto($product->ID, $product->NAME, convertStrToSlug($product->NAME))
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function getDetailSanPham($id, Request $request) {
        $isApiPublic = filter_var($request->input('IS_API_PUBLIC', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($isApiPublic && $this->useSapoStorefront()) {
            $sapoProduct = $this->sapoCatalogCache->getProduct((int) $id);
            if ($sapoProduct !== null) {
                return response()->json(
                    new ApiResponseDto(AppConstant::STATUS_SUCCESS
                        , 'Truy vấn thành công.'
                        , [
                            camelToSnakeUpper(class_basename(Product::class)) => SapoMapper::mapProduct($sapoProduct)
                        ]
                    )
                )->setStatusCode(JsonResponse::HTTP_OK);
            }
        }

        $product = $this->productRepository->getDetailSanPhamWithFetchEdger((int) $id);
        if ($product === null && $isApiPublic) {
            $localId = Product::query()->where('SAPO_ID', (int) $id)->value('ID');
            if ($localId) {
                $product = $this->productRepository->getDetailSanPhamWithFetchEdger((int) $localId);
            }
        }
        if ($product === null) {
            return response()->json(
                new ApiResponseDto(AppConstant::STATUS_FAILURE
                    , 'Sản phẩm không tồn tại.'
                    , [
                        camelToSnakeUpper(class_basename(Product::class)) => null
                    ]
                )
            )->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
        }
        $sanPhamDetail = null;

        switch ($product->ATTR49) {
            case AppConstant::TYPE_PRODUCT_COMMON:
                $sanPhamDetail = ProductMapper::mapProductDetailDtoFromEntity($product);
                break;
            default:
                # code...
                break;
        }
        
        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Truy vấn thành công.'
                , [
                    camelToSnakeUpper(class_basename(Product::class)) => $sanPhamDetail
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function getDetailBasicSanPham($id) {
        return $this->productRepository->getDetailBasicSanPham($id);
    }

    public function deleteSanPham($id, Request $request) {
        // Bắt đầu một transaction
        DB::beginTransaction();

        // Xóa mềm category product
        $product = self::getOrNewSanPham($id);
        $product->STATUS = AppConstant::STATUS_DELETED;
        $product->save();

        // Nếu mọi thứ thành công, commit transaction
        DB::commit();

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Xóa thành công.'
                , [
                    camelToSnakeUpper(class_basename(Product::class)) => null
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function getListSanPham(Request $request) {
        $draw = $request->input('DRAW', 1);
        $page = (int) $request->query('PAGE', 1);
        $perPage = (int) $request->query('PER_PAGE', 10);
        $isGetAllElements = filter_var($request->query('IS_GET_ALL_ELEMENTS', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($isGetAllElements === true) {
            $perPage = 2147483647;  
        }
        $tuKhoa = $request->input('TU_KHOA', null);
        $trangThaiHoatDong = $request->input('TRANG_THAI_HOAT_DONG', null);
        $trangThai = $request->input('TRANG_THAI', null);
        $arrDanhMucSanPhamId = $request->input('DANH_MUC_SAN_PHAM_ID', Array());
        $boLoc = $request->input('BO_LOC', null);
        $arrNotInId = $request->query('NOT_IN_ID');
        $productHot = filter_var($request->query('PRODUCT_HOT', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $productVip = filter_var($request->query('PRODUCT_VIP', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    
        $isApiPublic = filter_var($request->input('IS_API_PUBLIC', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($isApiPublic && $this->useSapoStorefront()) {
            return $this->getListSanPhamFromSapo($request, $draw, $page, $perPage, $tuKhoa, $arrDanhMucSanPhamId, $arrNotInId);
        }

        $resultPagination = $this->productRepository->getListSanPham($tuKhoa, $arrDanhMucSanPhamId, $trangThaiHoatDong
            , $boLoc
            , $arrNotInId
            , $productHot
            , $trangThai
            , $request
            , $isApiPublic
            , $page, $perPage
            , $productVip);
        
        
        // Mapping entity to dto
        $listProductDto = ProductMapper::mapListProductDetailFromPaginator($resultPagination->getCollection());
        if ($isApiPublic) {
            $this->attachInventoryVariants($listProductDto);
        }
        $resultPagination->setCollection($listProductDto);

        // Custom response pagination
        $customResponsePagination = PaginationUtils::pagination($resultPagination);
        $customResponsePagination['DRAW'] = $draw;

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Truy vấn thành công.'
                , [
                    camelToSnakeUpper(class_basename(Product::class)) => $customResponsePagination
                ]
                , JsonResponse::HTTP_OK
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    /**
     * Public storefront: lấy sản phẩm từ Sapo Admin API (Private App Basic Auth).
     * Docs: GET /admin/products.json?product_type=&collection_id=&status=active
     *
     * @param  array<int, mixed>|mixed  $arrDanhMucSanPhamId
     * @param  array<int, mixed>|mixed  $arrNotInId
     */
    private function getListSanPhamFromSapo(
        Request $request,
        mixed $draw,
        int $page,
        int $perPage,
        mixed $tuKhoa,
        mixed $arrDanhMucSanPhamId,
        mixed $arrNotInId
    ): JsonResponse {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $keyword = is_string($tuKhoa) ? trim($tuKhoa) : '';
        $productType = trim((string) config('services.sapo.product_type', 'Đồ chơi'));
        $categoryIds = [];
        if (is_array($arrDanhMucSanPhamId)) {
            foreach ($arrDanhMucSanPhamId as $id) {
                $n = (int) $id;
                if ($n > 0) {
                    $categoryIds[] = $n;
                }
            }
            $categoryIds = array_values(array_unique($categoryIds));
        }

        try {
            $collectionIds = $this->resolveSapoCollectionIds($categoryIds);
            $result = $this->sapoCatalogCache->listProducts($page, $perPage, $productType, $collectionIds, $keyword);
            $mapped = SapoMapper::mapProducts($result['products']);

            if (is_array($arrNotInId) && $arrNotInId !== []) {
                $exclude = array_map('intval', $arrNotInId);
                $mapped = array_values(array_filter($mapped, static function (ProductDetailDto $p) use ($exclude): bool {
                    return ! in_array((int) $p->id, $exclude, true);
                }));
            }

            $chiConHang = filter_var($request->input('CON_HANG', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($chiConHang === true) {
                $mapped = array_values(array_filter($mapped, static function (ProductDetailDto $p): bool {
                    return self::hasVariantInStock($p);
                }));
            }

            $paginator = new LengthAwarePaginator(
                $mapped,
                (int) $result['count'],
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $customResponsePagination = PaginationUtils::pagination($paginator);
            $customResponsePagination['DRAW'] = $draw;

            return response()->json(
                new ApiResponseDto(AppConstant::STATUS_SUCCESS
                    , 'Truy vấn thành công.'
                    , [
                        camelToSnakeUpper(class_basename(Product::class)) => $customResponsePagination
                    ]
                    , JsonResponse::HTTP_OK
                )
            )->setStatusCode(JsonResponse::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Sapo product list failed', ['message' => $e->getMessage()]);

            return response()->json(
                new ApiResponseDto(false
                    , 'Không lấy được dữ liệu Sapo: '.$e->getMessage()
                    , [
                        camelToSnakeUpper(class_basename(Product::class)) => [
                            'DATA' => [],
                            'CURRENT_PAGE' => $page,
                            'TOTAL_ITEM' => 0,
                            'PER_PAGE' => $perPage,
                            'TOTAL_PAGE' => 0,
                            'LINKS' => ['FIRST' => null, 'LAST' => null, 'NEXT' => null, 'PREVIOUS' => null],
                            'DRAW' => $draw,
                        ]
                    ]
                    , JsonResponse::HTTP_BAD_GATEWAY
                )
            )->setStatusCode(JsonResponse::HTTP_BAD_GATEWAY);
        }
    }

    /**
     * Map local menu category IDs → Sapo custom_collection IDs (ATTR2 hoặc khớp tên).
     *
     * @param  array<int, int>  $localCategoryIds
     * @return array<int, int>
     */
    private function resolveSapoCollectionIds(array $localCategoryIds): array
    {
        if ($localCategoryIds === []) {
            return [];
        }

        $rows = DB::table('category_p')
            ->whereIn('ID', $localCategoryIds)
            ->where('STATUS', AppConstant::STATUS_USING)
            ->get(['ID', 'NAME', 'ATTR2']);

        $ids = [];
        $needNameMatch = [];
        foreach ($rows as $row) {
            $sapoId = (int) ($row->ATTR2 ?? 0);
            if ($sapoId > 0) {
                $ids[] = $sapoId;
            } elseif ((int) $row->ID > 100000) {
                $ids[] = (int) $row->ID;
            } else {
                $needNameMatch[] = mb_strtolower(trim((string) $row->NAME));
            }
        }

        // ID gửi thẳng đã là collection Sapo (không có trong DB local)
        $foundLocal = $rows->pluck('ID')->map(static fn ($id) => (int) $id)->all();
        foreach ($localCategoryIds as $id) {
            if ($id > 100000 && ! in_array($id, $foundLocal, true)) {
                $ids[] = $id;
            }
        }

        if ($needNameMatch !== []) {
            try {
                $collections = $this->sapoService->getCustomCollections(['limit' => 250, 'page' => 1]);
                foreach ($collections as $collection) {
                    if (! is_array($collection)) {
                        continue;
                    }
                    $name = mb_strtolower(trim((string) ($collection['name'] ?? '')));
                    if ($name !== '' && in_array($name, $needNameMatch, true)) {
                        $cid = (int) ($collection['id'] ?? 0);
                        if ($cid > 0) {
                            $ids[] = $cid;
                        }
                    }
                }
            } catch (Throwable $e) {
                Log::warning('Sapo resolve collections by name failed', ['message' => $e->getMessage()]);
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    public function activeSanPham($id, Request $request) {
        // Bắt đầu một transaction
        DB::beginTransaction();
        
        // Get thông tin chi tiết category product
        $product = $this->productRepository->getDetailSanPham($id);
        $product->IS_ACTIVE = filter_var($request->input('IS_ACTIVE') ?? true, FILTER_VALIDATE_BOOLEAN);
        $product->save();

        // Nếu mọi thứ thành công, commit transaction
        DB::commit();

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Chuyển đổi trạng thái thành công.'
                , [
                    class_basename(Product::class) => null
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function soldSanPham($id, Request $request) {
        // Bắt đầu một transaction
        DB::beginTransaction();
        
        // Get thông tin chi tiết category product
        $product = $this->productRepository->getDetailSanPham($id);
        $product->STATUS = $request->input('STATUS');
        $product->save();

        // Nếu mọi thứ thành công, commit transaction
        DB::commit();

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Sản phẩm đã bán thành công.'
                , [
                    class_basename(Product::class) => null
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    private function attachInventoryVariants($products): void
    {
        $productIds = $products->pluck('id')->map('intval')->filter()->values();
        if ($productIds->isEmpty()) {
            return;
        }

        $variants = ProductVariant::query()
            ->whereIn('PRODUCT_ID', $productIds)
            ->where('STATUS', AppConstant::STATUS_USING)
            ->where('IS_ACTIVE', true)
            ->orderBy('ID')
            ->get()
            ->groupBy('PRODUCT_ID');

        foreach ($products as $productDto) {
            $productDto->danhSachBienThe = ($variants[(int) $productDto->id] ?? collect())
                ->map(static function (ProductVariant $variant): ProductVariantDetailDto {
                    $stock = max(0, (int) $variant->INVENTORY_QUANTITY);
                    $dto = ProductVariantDetailDto::createEmpty();
                    $dto->id = (int) $variant->ID;
                    $dto->productId = (int) $variant->PRODUCT_ID;
                    $dto->title = (string) ($variant->ATTR4 ?: 'Mặc định');
                    $dto->sapoVariantId = (int) $variant->SAPO_VARIANT_ID ?: null;
                    $dto->sku = $variant->SKU;
                    $dto->inventoryQuantity = $stock;
                    $dto->optionValues = $variant->OPTION_VALUES;
                    $dto->productStatus = $stock > 0 ? 'CON_HANG' : 'HET_HANG';
                    $dto->productColor = $variant->PRODUCT_COLOR;
                    $dto->isContactPrice = (bool) $variant->IS_CONTACT_PRICE;
                    $dto->productPrice = $variant->PRODUCT_PRICE;
                    $dto->productOriginalPrice = $variant->PRODUCT_ORIGINAL_PRICE;
                    $dto->isInStock = $stock > 0;
                    $dto->isActive = true;
                    $dto->danhSachHinhAnhDaiDien = [];

                    return $dto;
                })
                ->values()
                ->all();
        }
    }

    /** Sản phẩm còn bán được khi có ít nhất một biến thể còn tồn kho. */
    private static function hasVariantInStock(ProductDetailDto $product): bool
    {
        $variants = $product->danhSachBienThe;
        if (! is_array($variants) || $variants === []) {
            return false;
        }

        foreach ($variants as $variant) {
            $stock = 0;
            $inStock = false;
            if ($variant instanceof ProductVariantDetailDto) {
                $stock = (int) $variant->inventoryQuantity;
                $inStock = (bool) $variant->isInStock;
            } elseif (is_array($variant)) {
                $stock = (int) ($variant['SO_LUONG_TON'] ?? 0);
                $inStock = (bool) ($variant['CON_HANG'] ?? false);
            }

            if ($stock > 0 && $inStock) {
                return true;
            }
        }

        return false;
    }

    private function useSapoStorefront(): bool
    {
        return $this->sapoService->isEnabled()
            && strtolower((string) config('services.sapo.storefront_source', 'local')) === 'sapo';
    }

}