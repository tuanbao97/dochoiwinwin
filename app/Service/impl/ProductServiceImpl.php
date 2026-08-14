<?php

namespace App\Service\impl;

use App\Dto\product\ProductBdsDatDetailDto;
use App\Dto\product\ProductDetailDto;
use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Mapper\ProductMapper;
use App\Mapper\SapoMapper;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductDocumentStorageRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductVariantRepository;
use App\Service\ProductService;
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

    /**
     * Create a new class instance.
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductDocumentStorageRepository $productDocumentStorageRepository,
        ProductCategoryRepository $productCategoryRepository,
        ProductVariantRepository $productVariantRepository,
        SapoService $sapoService
    ) {
        $this->productRepository = $productRepository;
        $this->productDocumentStorageRepository = $productDocumentStorageRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->sapoService = $sapoService;
    }

    public function getOrNewSanPham($id): Product
    {
        $product = ($id != null) ? $this->productRepository->getDetailSanPham($id) : new Product();
        return $product;
    }

    public function getOrNewSanPhamWithFetchEdger($id): Product
    {
        $product = ($id != null) ? $this->productRepository->getDetailSanPhamWithFetchEdger($id) : new Product();
        return $product;
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
        $arrDanhMucSanPham = [
            [
                'PRODUCT_ID' => $product->ID
                , 'CATEGORY_ID' => $request->input('DANH_MUC_SAN_PHAM.ID')
                , 'IS_ACTIVE' => true
            ]
        ];
        // Lưu danh mục sản phẩm
        self::handleSaveDanhMucSanPhams($product->ID, $arrDanhMucSanPham);



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
        if ($isApiPublic && $this->sapoService->isEnabled()) {
            $sapoProduct = $this->sapoService->getProduct((int) $id);
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

        $product = $this->productRepository->getDetailSanPhamWithFetchEdger($id);
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

        if ($isApiPublic && $this->sapoService->isEnabled()) {
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
        // TODO: tạm hardcode filter SAPO — bỏ khi nối lại PAGE/PER_PAGE/TU_KHOA từ request
        $filter = [
            'product_types' => ['Đồ chơi'],
            'query' => '',
            'page' => 10,
            'limit' => 20,
        ];

        $page = $filter['page'];
        $perPage = $filter['limit'];

        $query = [
            'page' => $page,
            'limit' => $perPage,
            'product_types' => $filter['product_types'],
            'query' => $filter['query'],
        ];

        try {
            $result = $this->sapoService->getProducts($query);
            $mapped = SapoMapper::mapProducts($result['products']);

            if (is_array($arrNotInId) && $arrNotInId !== []) {
                $exclude = array_map('intval', $arrNotInId);
                $mapped = array_values(array_filter($mapped, static function (ProductDetailDto $p) use ($exclude): bool {
                    return ! in_array((int) $p->id, $exclude, true);
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

}