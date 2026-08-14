<?php

namespace App\Service\impl;

use App\Dto\response\ApiResponseDto;
use App\Enum\AppConstant;
use App\Http\Requests\categoryp\CategoryPSaveRequest;
use App\Mapper\CategoryPMapper;
use App\Models\CategoryP;
use App\Repository\CategoryPDocumentStorageRepository;
use App\Repository\CategoryPRepository;
use App\Service\CategoryPService;
use App\Service\SapoService;
use App\Utils\PaginationUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPServiceImpl implements CategoryPService
{
    private CategoryPRepository $categoryPRepository;

    private CategoryPDocumentStorageRepository $categoryPDocumentStorageRepository;

    private SapoService $sapoService;

    public function __construct(
        CategoryPRepository $categoryPRepository,
        CategoryPDocumentStorageRepository $categoryPDocumentStorageRepository,
        SapoService $sapoService
    ) {
        $this->categoryPRepository = $categoryPRepository;
        $this->categoryPDocumentStorageRepository = $categoryPDocumentStorageRepository;
        $this->sapoService = $sapoService;
    }

    /**
     * Get list category tree
     *
     * @param Request $request
     */
    public function getListDanhMucSanPhamTree(Request $request)
    {
        // Get input
        $page = $request->query('PAGE', 1);
        $perPage = $request->query('PER_PAGE', 10);
        $isGetAllElements = $request->input('IS_GET_ALL_ELEMENTS', false);
        if ($isGetAllElements === true) {
            $perPage = 2147483647;  
        }

        $isApiPublic = filter_var($request->input('IS_API_PUBLIC', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        // Menu / section danh mục luôn lấy từ DB local (seed folder Đồ chơi Win Win).
        // Sapo chỉ dùng cho sản phẩm, không override cây danh mục.
        $resultPagination = $this->categoryPRepository->getListDanhMucSanPhamWithChilds($isApiPublic, $page, $perPage);

        // Mapping entity to dto
        $listCategoryPDetailDto = CategoryPMapper::mapListCategoryPDetailFromPaginator($resultPagination->getCollection(), true);
        $resultPagination->setCollection($listCategoryPDetailDto);
        
        // Custom response pagination
        $customResponsePagination = PaginationUtils::pagination($resultPagination);
        
        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Truy vấn thành công.'
                , [
                    camelToSnakeUpper(class_basename(CategoryP::class)) => $customResponsePagination
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function getListDanhMucSanPham(Request $request)
    {
        // Get input
        $isGetAllParentAndChilds = $request->input('IS_GET_ALL_PARENT_AND_CHILDS', false);
        $page = $request->query('PAGE', 1);
        $perPage = $request->query('PER_PAGE', 10);
        $isGetAllElements = $request->input('IS_GET_ALL_ELEMENTS', false);
        if ($isGetAllElements === true) {
            $perPage = 2147483647;  
        }
       
        $parentId = $request->input('PARENT_ID', null);
        $trangThaiHoatDong = $request->input('TRANG_THAI_HOAT_DONG', null);
        $tuKhoa = $request->input('TU_KHOA', null);

        $isApiPublic = filter_var($request->input('IS_API_PUBLIC', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $resultPagination = $this->categoryPRepository->getListDanhMucSanPham($isGetAllParentAndChilds, $parentId, $trangThaiHoatDong, $tuKhoa
            , $isApiPublic
            ,$page, $perPage);

        // Mapping entity to dto
        $listProductDto = CategoryPMapper::mapListCategoryPDetailFromPaginator($resultPagination->getCollection());
        $resultPagination->setCollection($listProductDto);

        // Custom response pagination
        $customResponsePagination = PaginationUtils::pagination($resultPagination);
        
        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Truy vấn thành công.'
                , [
                    camelToSnakeUpper(class_basename(CategoryP::class)) => $customResponsePagination
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    /**
     * Save category product
     *
     * @param CategoryPSaveRequest $request
     */
    public function save(CategoryPSaveRequest $request)
    {
        // Bắt đầu một transaction
        DB::beginTransaction();

        // Mapper đối tượng
        $data = $request->all();
        $id = isset($data['ID']) ? $data['ID'] : null;
        
        $categoryP = self::getOrNewDanhMucSanPham($id);
        CategoryPMapper::mapFromArray($categoryP, $data);
        
        // Set SORT_ORDER
        // if ($categoryP->ID == null) self::setThuTuSapXep($categoryP);
        
        // Save
        $categoryP = $this->categoryPRepository->save($categoryP->toArray());
        $categoryP = is_null($id) ? $categoryP : $categoryP->first();

        // Update tất cả record nào đang sử dụng parent_id này về null
        // $this->categoryPRepository->updateParentIdToNullById($categoryP->ID);

        // Xóa document storage by category-p id
        self::deleteAllDanhMucSanPhamFileDinhKem($categoryP->ID);
        
        // Xử lý lưu danh sách hình ảnh đại diện upload vào database
        $danhSachHinhAnhDaiDienUpload = $request->input('DANH_SACH_HINH_ANH_DAI_DIEN', null);
        if (!is_null($danhSachHinhAnhDaiDienUpload) && count($danhSachHinhAnhDaiDienUpload) > 0) {
            // Lưu hình ảnh vào database
            self::handleSaveFileDinhKem($categoryP->ID, $danhSachHinhAnhDaiDienUpload);
        }

        // Log
        Log::info("CategoryP ID {$categoryP->ID} save successfully!");
        
        // Nếu mọi thứ thành công, commit transaction
        DB::commit();

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , is_null($id) ? 'Tạo mới thành công.' : 'Cập nhật thành công.'
                , [
                    class_basename(CategoryP::class) => $categoryP
                ]
                )
            )->setStatusCode(JsonResponse::HTTP_OK);
    }

    private function handleSaveFileDinhKem($categoryPId, array $documentStorages) {
        // Lưu document storages
        if (isset($documentStorages) && count($documentStorages) > 0) {
            $this->categoryPDocumentStorageRepository->saveCategoryPDocumentStorages($categoryPId, $documentStorages);
        }
    }

    /**
     * Set sort order
     * 
     * @param CategoryP $categoryP
     */
    public function setThuTuSapXep(CategoryP $categoryP) {
        $categoryP->SORT_ORDER = self::getThuTuSapXepMoiNhat();
    }

    /**
     * Get sort order lastest
     *
     * @param CategoryP $categoryP
     */
    public function getThuTuSapXepMoiNhat()
    {
        $sortOrder = $this->categoryPRepository->getSortOrder() + 1;
        Log::info("Sort order $sortOrder");
        return $sortOrder;
    }

    /**
     * Get or new categor product
     *
     * @param int $id
     * @return CategoryP
     */
    public function getOrNewDanhMucSanPham($id): CategoryP
    {
        $categoryP = ($id != null) ? $this->categoryPRepository->find($id)->first() : new CategoryP();
        return $categoryP;
    }

    public function deleteAllDanhMucSanPhamFileDinhKem($categoryPId) : bool
    {
        // Xóa document storage by category-p id
        return $this->categoryPDocumentStorageRepository->deleteAllDanhMucSanPhamFileDinhKem($categoryPId);
    }

    public function getDetailDanhMucSanPham($id, Request $request)
    {
        $categoryP = $this->categoryPRepository->getDetailDanhMucSanPham($id);
        // Lấy thêm thông tin query document storages
        $categoryP->avatars;


        // Mapping entity to dto
        $categoryPDetailDto = CategoryPMapper::mapCategoryPDetailFromEntity($categoryP);
        
        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Truy vấn thành công.'
                , [
                    camelToSnakeUpper(class_basename(CategoryP::class)) => $categoryPDetailDto
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function deleteDanhMucSanPham($id, Request $request)
    {
        // Bắt đầu một transaction
        DB::beginTransaction();

        // Xóa mềm category product
        $categoryP = self::getOrNewDanhMucSanPham($id);
        $categoryP->STATUS = AppConstant::STATUS_DELETED;
        $categoryP->save();

        // Xóa cứng pivot table category_p_document_storages
        // self::deleteAllDanhMucSanPhamFileDinhKem($id);

        // Nếu mọi thứ thành công, commit transaction
        DB::commit();

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Xóa thành công.'
                , [
                    class_basename(CategoryP::class) => null
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }

    public function activeTrangThaiHoatDong($id, Request $request)
    {
        // Bắt đầu một transaction
        DB::beginTransaction();
        
        // Get thông tin chi tiết category product
        $categoryP = $this->categoryPRepository->getDetailDanhMucSanPham($id);
        $categoryP->IS_ACTIVE = filter_var($request->input('IS_ACTIVE') ?? true, FILTER_VALIDATE_BOOLEAN);
        $categoryP->save();

        // Nếu mọi thứ thành công, commit transaction
        DB::commit();

        return response()->json(
            new ApiResponseDto(AppConstant::STATUS_SUCCESS
                , 'Chuyển đổi trạng thái thành công.'
                , [
                    class_basename(CategoryP::class) => null
                ]
            )
        )->setStatusCode(JsonResponse::HTTP_OK);
    }


}
