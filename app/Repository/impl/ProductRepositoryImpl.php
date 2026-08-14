<?php

namespace App\Repository\impl;

use App\Dto\sort\SortRequestDto;
use App\Enum\AppConstant;
use App\Models\CategoryP;
use App\Models\Product;
use App\Repository\BaseRepository;
use App\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepositoryImpl extends BaseRepository implements ProductRepository
{
    public function getModel()
    {
        return Product::class;
    }

    public function getDetailSanPham(int $id) : Product {
        return Product::query()
        ->where('ID', $id)
        ->whereIn('STATUS', [AppConstant::STATUS_USING, AppConstant::STATUS_SOLD])
        ->first();
    }

    public function getDetailBasicSanPham(int $id) : ?Product {
        $query = Product::query()
                ->from('product AS p')
                // Eager load danh sách hình ảnh đại diện (DANH_SACH_HINH_ANH_DAI_DIEN)
                ->with(['avatars' => function ($q) {
                    $q->select([
                        'document_storage.ID',
                        'document_storage.NAME',
                        'document_storage.DIRECTORY',
                        'document_storage.ORIGINAL_NAME',
                        'document_storage.EXTENSION',
                        'document_storage.PATH',
                        'document_storage.UPD_DT',
                    ])
                    // Lấy thêm ATTR2 (ASPECT_RATIO) từ pivot
                    ->withPivot(['ATTR2']);
                }])
                ->join('product_category AS pc', function ($join) {
                    $join->on('p.id', '=', 'pc.product_id')
                        ->where('pc.STATUS', '=', AppConstant::STATUS_USING);
                })
                ->where('p.ID', $id)
                ->whereIn('p.STATUS', [AppConstant::STATUS_USING, AppConstant::STATUS_SOLD])
                ->select(
                    'p.*',
                    'pc.CATEGORY_ID as CATEGORY_ID'
                );
        return $query->first();
    }

    public function getDetailSanPhamWithFetchEdger(int $id) : ?Product {
        return Product::query()
        ->from('product AS p')
        ->with(['avatars', 'images', 'videos', 'files', 'categories', 'variants.productImage'])
        ->where('p.ID', $id)
        ->whereIn('p.STATUS', [AppConstant::STATUS_USING, AppConstant::STATUS_SOLD])
        ->select('p.*')
        ->first();
    }

    public function getListSanPham(?string $tuKhoa, ?array $arrDanhMucSanPhamId, ?bool $trangThaiHoatDong
        , ?string $boLoc
        , ?array $arrNotInId
        , ?bool $productHot
        , ?string $trangThai = null
        , Request $request
        , ?bool $isApiPublic
        , int $page, int $perPage
        , ?bool $productVip = null) : LengthAwarePaginator {
        
        // Ảnh hover (ảnh thứ 2 của card): lấy 1 ảnh DANH_SACH_HINH_ANH đầu tiên mỗi sản phẩm
        $hoverImageQuery = DB::table('product_document_storage AS pds')
                ->selectRaw('pds.PRODUCT_ID, pds.DOCUMENT_STORAGE_ID, pds.SORT_ORDER, pds.ATTR1, pds.ATTR2'
                    . ', ROW_NUMBER() OVER (PARTITION BY pds.PRODUCT_ID ORDER BY pds.SORT_ORDER ASC, pds.ID ASC) AS ROW_NUM')
                ->where('pds.ATTR1', '=', 'DANH_SACH_HINH_ANH')
                ->where('pds.STATUS', '=', AppConstant::STATUS_USING);

        // Main query - không còn product_variant
        $query = DB::table('product AS p')
                ->join('product_category AS pc', function ($join) {
                    $join->on('p.id', '=', 'pc.product_id')
                        ->where('pc.STATUS', '=', AppConstant::STATUS_USING);
                })
                ->join('category_p AS cp', function ($join) {
                    $join->on('cp.id', '=', 'pc.category_id')
                        ->where('cp.STATUS', '=', AppConstant::STATUS_USING);
                })
                ->leftJoin('product_document_storage as pds_avatar', function ($join) {
                    $join->on('p.id', '=', 'pds_avatar.product_id')
                        ->where('pds_avatar.attr1', '=', 'DANH_SACH_HINH_ANH_DAI_DIEN')
                        ->where('pds_avatar.IS_THUMNAIL', true)
                        ->where('pds_avatar.STATUS', '=', AppConstant::STATUS_USING);
                })
                ->leftJoin('document_storage as ds_avatar', function ($join) {
                    $join->on('ds_avatar.ID', '=', 'pds_avatar.DOCUMENT_STORAGE_ID');
                })
                ->leftJoinSub($hoverImageQuery, 'pds_hover', function ($join) {
                    $join->on('p.id', '=', 'pds_hover.PRODUCT_ID')
                        ->where('pds_hover.ROW_NUM', '=', 1);
                })
                ->leftJoin('document_storage as ds_hover', function ($join) {
                    $join->on('ds_hover.ID', '=', 'pds_hover.DOCUMENT_STORAGE_ID');
                })
                ->whereIn('p.STATUS', [AppConstant::STATUS_USING, AppConstant::STATUS_SOLD])
                ->distinct()
                ->select(
                    'p.*',

                    'ds_avatar.ID as OBJ_AVATAR_ID',
                    'ds_avatar.NAME as OBJ_AVATAR_NAME',
                    'ds_avatar.ORIGINAL_NAME as OBJ_AVATAR_ORIGINAL_NAME',
                    'ds_avatar.EXTENSION as OBJ_AVATAR_EXTENSION',
                    'ds_avatar.PATH as OBJ_AVATAR_PATH',
                    'ds_avatar.DIRECTORY as OBJ_AVATAR_DIRECTORY',
                    'ds_avatar.SIZE as OBJ_AVATAR_SIZE',
                    'ds_avatar.MD5 as OBJ_AVATAR_MD5',
                    'ds_avatar.TYPE_FILE as OBJ_AVATAR_TYPE_FILE',
                    'ds_avatar.DESCRIPTION as OBJ_AVATAR_DESCRIPTION',
                    'ds_avatar.CRT_ID as OBJ_AVATAR_CRT_ID',
                    'ds_avatar.CRT_NAME as OBJ_AVATAR_CRT_NAME',
                    'ds_avatar.CRT_DT as OBJ_AVATAR_CRT_DT',
                    'ds_avatar.UPD_ID as OBJ_AVATAR_UPD_ID',
                    'ds_avatar.UPD_NAME as OBJ_AVATAR_UPD_NAME',
                    'ds_avatar.UPD_DT as OBJ_AVATAR_UPD_DT',
                    'ds_avatar.IS_ACTIVE as OBJ_AVATAR_IS_ACTIVE',
                    'pds_avatar.IS_THUMNAIL as OBJ_AVATAR_IS_THUMNAIL',
                    'pds_avatar.SORT_ORDER as OBJ_AVATAR_SORT_ORDER',
                    'pds_avatar.ATTR1 as OBJ_AVATAR_TYPE',
                    'pds_avatar.ATTR2 as OBJ_AVATAR_ASPECT_RATIO',

                    'ds_hover.ID as OBJ_HOVER_ID',
                    'ds_hover.NAME as OBJ_HOVER_NAME',
                    'ds_hover.ORIGINAL_NAME as OBJ_HOVER_ORIGINAL_NAME',
                    'ds_hover.EXTENSION as OBJ_HOVER_EXTENSION',
                    'ds_hover.PATH as OBJ_HOVER_PATH',
                    'ds_hover.DIRECTORY as OBJ_HOVER_DIRECTORY',
                    'ds_hover.SIZE as OBJ_HOVER_SIZE',
                    'ds_hover.MD5 as OBJ_HOVER_MD5',
                    'ds_hover.TYPE_FILE as OBJ_HOVER_TYPE_FILE',
                    'ds_hover.DESCRIPTION as OBJ_HOVER_DESCRIPTION',
                    'ds_hover.CRT_ID as OBJ_HOVER_CRT_ID',
                    'ds_hover.CRT_NAME as OBJ_HOVER_CRT_NAME',
                    'ds_hover.CRT_DT as OBJ_HOVER_CRT_DT',
                    'ds_hover.UPD_ID as OBJ_HOVER_UPD_ID',
                    'ds_hover.UPD_NAME as OBJ_HOVER_UPD_NAME',
                    'ds_hover.UPD_DT as OBJ_HOVER_UPD_DT',
                    'ds_hover.IS_ACTIVE as OBJ_HOVER_IS_ACTIVE',
                    'pds_hover.SORT_ORDER as OBJ_HOVER_SORT_ORDER',
                    'pds_hover.ATTR1 as OBJ_HOVER_TYPE',
                    'pds_hover.ATTR2 as OBJ_HOVER_ASPECT_RATIO',

                    
                    'cp.ID as OBJ_CATEGORY_ID',
                    'cp.NAME as OBJ_CATEGORY_NAME',
                    'cp.PARENT_ID as OBJ_CATEGORY_PARENT_ID',
                    'cp.SORT_ORDER as OBJ_CATEGORY_SORT_ORDER',
                    'cp.DESCRIPTION as OBJ_CATEGORY_DESCRIPTION',
                    'cp.TREE_LEVEL as OBJ_CATEGORY_TREE_LEVEL',
                    'cp.CRT_ID as OBJ_CATEGORY_CRT_ID',
                    'cp.CRT_NAME as OBJ_CATEGORY_CRT_NAME',
                    'cp.CRT_DT as OBJ_CATEGORY_CRT_DT',
                    'cp.UPD_ID as OBJ_CATEGORY_UPD_ID',
                    'cp.UPD_NAME as OBJ_CATEGORY_UPD_NAME',
                    'cp.UPD_DT as OBJ_CATEGORY_UPD_DT',
                    'cp.IS_ACTIVE as OBJ_CATEGORY_IS_ACTIVE'
                );


        if (!blank($arrNotInId)) {
            $query->whereNotIn('p.ID', $arrNotInId);
        }

        $arrDanhSachSanPhamId = $request->query('DANH_SACH_SAN_PHAM_ID');
        $filterByProductIds = false;
        if (!blank($arrDanhSachSanPhamId) && is_array($arrDanhSachSanPhamId)) {
            $arrDanhSachSanPhamId = array_values(array_unique(array_filter(
                array_map('intval', $arrDanhSachSanPhamId),
                fn (int $id) => $id > 0
            )));
            if (count($arrDanhSachSanPhamId) > 0) {
                $filterByProductIds = true;
                $query->whereIn('p.ID', $arrDanhSachSanPhamId);
            }
        }

        if (!blank($tuKhoa)) {
            $query->where(
                function($query) use ($tuKhoa) {
                    $query->where([
                        ['p.NAME', 'like', '%' . $tuKhoa . '%']
                    ])->orWhere([
                        ['p.PRICE', 'like', '%' . $tuKhoa . '%']
                    ])->orWhere([
                        ['p.PRICE_RENT', 'like', '%' . $tuKhoa . '%']
                    ])->orWhere([
                        ['p.PRICE_RENT_DEPOSIT_AMOUNT', 'like', '%' . $tuKhoa . '%']
                    ])
                    ->orWhere([
                        ['p.PRODUCT_QUANTITY', 'like', '%' . $tuKhoa . '%']
                    ])
                    ->orWhere([
                        ['p.PRODUCT_TAGS', 'like', '%' . $tuKhoa . '%']
                    ])
                    ->orWhere([
                        ['p.POST_TITLE', 'like', '%' . $tuKhoa . '%']
                    ])
                    /* ->orWhere([
                        ['p.POST_DETAIL_DESCRIPTION', 'like', '%' . $tuKhoa . '%']
                    ]) */
                    ;
                }
            );
        }

        if ($productHot === true) {
            $query->where([
                ['p.PRODUCT_HOT', true]
            ]);
        }

        if ($productVip === true) {
            $query->where([
                ['p.PRODUCT_VIP', true]
            ]);
        }

        
        
        if ($isApiPublic === true) {
            $query->where([
                ['p.IS_ACTIVE', true],
                ['cp.IS_ACTIVE', true],
            ]);
        }
        
        if (!is_null($arrDanhMucSanPhamId) && is_array($arrDanhMucSanPhamId) && count($arrDanhMucSanPhamId) > 0) {
            // Lấy toàn bộ ID danh mục con
            $allCategoryIds = $arrDanhMucSanPhamId;
            $childIds = CategoryP::getAllChildCategoryIds($arrDanhMucSanPhamId, [], $isApiPublic);
            $allCategoryIds = array_merge($allCategoryIds, $childIds);

            $query->whereIn('cp.ID', $allCategoryIds);
        }

        if (!is_null($trangThaiHoatDong)) {
            $query->where([
                ['p.IS_ACTIVE', '=', $trangThaiHoatDong]
            ]);
        }

        if (!is_null($trangThai)) {
            $query->where([
                ['p.STATUS', '=', $trangThai]
            ]);
        }

        $arrMucGia = $request->input('MUC_GIA', null);
        if (!is_null($arrMucGia) && count($arrMucGia) > 0) {

            $query->where(
                function($q) use ($arrMucGia) {
                    foreach ($arrMucGia as $index => $mucGia) {
                        $minValue = $mucGia['MIN_VALUE'] ?? null;
                        $maxValue = $mucGia['MAX_VALUE'] ?? null;
                        // Mặc định bao gồm biên (>= / <=). Có thể tắt bằng MIN_INCLUSIVE / MAX_INCLUSIVE = false
                        $minInclusive = !array_key_exists('MIN_INCLUSIVE', $mucGia)
                            || filter_var($mucGia['MIN_INCLUSIVE'], FILTER_VALIDATE_BOOLEAN);
                        $maxInclusive = !array_key_exists('MAX_INCLUSIVE', $mucGia)
                            || filter_var($mucGia['MAX_INCLUSIVE'], FILTER_VALIDATE_BOOLEAN);

                        $q->orWhere(
                            function($subquery) use ($minValue, $maxValue, $minInclusive, $maxInclusive) {
                                 if (!is_null($minValue) && $minValue !== '') {
                                    $subquery->where(
                                        'p.PRICE',
                                        $minInclusive ? '>=' : '>',
                                        (float) $minValue
                                    );
                                }

                                if (!is_null($maxValue) && $maxValue !== '') {
                                    $subquery->where(
                                        'p.PRICE',
                                        $maxInclusive ? '<=' : '<',
                                        (float) $maxValue
                                    );
                                }
                            }
                        );
                        
                    }
                }
            );
        }

        $sortDto = SortRequestDto::fromRequest($request);
        if ($filterByProductIds) {
            $query->orderByRaw('FIELD(p.ID, ' . implode(',', $arrDanhSachSanPhamId) . ')');
        } else if (!blank($boLoc)) {
            // Luôn đẩy sản phẩm đã bán (SOLD) xuống cuối
            $query->orderByRaw('CASE WHEN p.STATUS = ? THEN 1 ELSE 0 END', [AppConstant::STATUS_SOLD]);
            switch ($boLoc) {
                case 'default':
                    if ($isApiPublic === true) {
                        // UI mặc định: giá tăng dần (giá liên hệ / không giá cuối), rồi nổi bật, rồi cập nhật mới nhất
                        $query->orderByRaw('CASE WHEN p.PRICE IS NULL OR p.PRICE <= 0 THEN 1 ELSE 0 END ASC');
                        $query->orderByRaw('COALESCE(NULLIF(p.PRICE, 0), 999999999999999) ASC');
                        $query->orderByRaw('CASE WHEN p.PRODUCT_HOT = ? THEN 0 ELSE 1 END', [true]);
                        $query->orderBy('p.UPD_DT', 'desc');
                    } else {
                        // Admin/BE: giữ sort cũ theo ngày tạo
                        $query->orderBy('p.CRT_DT', 'desc');
                    }
                    break;
                case 'gia-tang':
                    // Giá bán tăng dần; sản phẩm không giá / liên hệ (PRICE null|<=0) xuống cuối
                    $query->orderByRaw('CASE WHEN p.PRICE IS NULL OR p.PRICE <= 0 THEN 1 ELSE 0 END ASC');
                    $query->orderByRaw('COALESCE(NULLIF(p.PRICE, 0), 999999999999999) ASC');
                    $query->orderBy('p.ID', 'asc');
                    break;
                case 'gia-giam':
                    // Giá bán giảm dần; sản phẩm không giá / liên hệ xuống cuối
                    $query->orderByRaw('CASE WHEN p.PRICE IS NULL OR p.PRICE <= 0 THEN 1 ELSE 0 END ASC');
                    $query->orderByRaw('COALESCE(NULLIF(p.PRICE, 0), -1) DESC');
                    $query->orderBy('p.ID', 'desc');
                    break;
                case 'a-z':
                    $query->orderBy('p.NAME', 'asc');
                    break;
                case 'z-a':
                    $query->orderBy('p.NAME', 'desc');
                    break;
                case 'cu-den-moi':
                    $query->orderBy('p.UPD_DT', 'asc');
                    break;
                case 'moi-den-cu':
                    $query->orderBy('p.UPD_DT', 'desc');
                    break;
                default:
                    // Fallback: theo thời gian tạo mới nhất
                    $query->orderBy('p.CRT_DT', 'desc');
                    break;
            }
        } else if (!is_null($sortDto) && !is_null($sortDto->fieldName)) {
            // Luôn đẩy sản phẩm đã bán (SOLD) xuống cuối
            $query->orderByRaw('CASE WHEN p.STATUS = ? THEN 1 ELSE 0 END', [AppConstant::STATUS_SOLD]);
            $fieldName = $sortDto->fieldName;
            $sortType = $sortDto->sortType;

            switch ($fieldName) {
                case 'STT':
                    $query->orderBy('p.CRT_DT', strtolower($sortType) == 'desc' ? 'asc' : 'desc');
                    break;
                case 'DANH_SACH_HINH_ANH_DAI_DIEN':
                    // Không sort theo hình ảnh vì đây là array
                    break;
                case 'FULL_DU_LIEU':
                    // Không sort theo dữ liệu đầy đủ
                    break;
                case 'DANH_MUC_SAN_PHAM':
                    $query->orderBy('cp.NAME', strtolower($sortType));
                    break;
                case 'TEN_SAN_PHAM':
                    $query->orderBy('p.NAME', strtolower($sortType));
                    break;
                case 'GIA_CA':
                    $query->orderBy('p.PRICE', strtolower($sortType));
                    break;
                case 'TRANG_THAI':
                    $query->orderBy('p.STATUS', strtolower($sortType));
                    break;
                case 'TRANG_THAI_HOAT_DONG':
                    $query->orderBy('p.IS_ACTIVE', strtolower($sortType));
                    break;
                
                default:
                    // Fallback: theo thời gian tạo mới nhất
                    $query->orderBy('p.CRT_DT', 'desc');
                    break;
            } 
        } else {
            // Không có BO_LOC: SOLD cuối; frontend = giá tăng (liên hệ cuối) → nổi bật → UPD_DT
            $query->orderByRaw('CASE WHEN p.STATUS = ? THEN 1 ELSE 0 END', [AppConstant::STATUS_SOLD]);
            if ($isApiPublic === true) {
                $query->orderByRaw('CASE WHEN p.PRICE IS NULL OR p.PRICE <= 0 THEN 1 ELSE 0 END ASC');
                $query->orderByRaw('COALESCE(NULLIF(p.PRICE, 0), 999999999999999) ASC');
                $query->orderByRaw('CASE WHEN p.PRODUCT_HOT = ? THEN 0 ELSE 1 END', [true]);
                $query->orderBy('p.UPD_DT', 'desc');
            } else {
                $query->orderBy('p.CRT_DT', 'desc');
            }
        }

        $query = $query->paginate($perPage, ['*'], 'page', $page);
        return $query;
    }

    public function getListSanPhamBackup(?string $tuKhoa, ?array $arrDanhMucSanPhamId, ?bool $trangThaiHoatDong
        , ?string $tinhThanhPho, ?string $quanHuyen, ?string $phuongXaThiTran
        , ?string $boLoc, ?string $loai
        , ?array $arrNotInId
        , ?bool $productHot
        , Request $request
        , bool $isApiPublic
        , int $page, int $perPage) : LengthAwarePaginator {
        $query = Product::query()
        ->with(['avatars','categories'])
        ->whereIn('STATUS', [AppConstant::STATUS_USING, AppConstant::STATUS_SOLD]);

        if (!blank($loai)) {
            $query->where([
                ['TYPE', '=', $loai]
            ]);
        }

        if (!blank($arrNotInId)) {
            $query->whereNotIn('ID', $arrNotInId);
        }

        if (!blank($tuKhoa)) {
            $query->where(
                function($query) use ($tuKhoa) {
                    $query->where([
                        ['NAME', 'like', '%' . $tuKhoa . '%']
                    ])->orWhere([
                        ['PRICE', 'like', '%' . $tuKhoa . '%']
                    ])->orWhere([
                        ['PRICE_RENT', 'like', '%' . $tuKhoa . '%']
                    ])->orWhere([
                        ['PRICE_RENT_DEPOSIT_AMOUNT', 'like', '%' . $tuKhoa . '%']
                    ])
                    ->orWhere([
                        ['PRODUCT_QUANTITY', 'like', '%' . $tuKhoa . '%']
                    ])
                    ->orWhere([
                        ['PRODUCT_TAGS', 'like', '%' . $tuKhoa . '%']
                    ])
                    ->orWhere([
                        ['POST_TITLE', 'like', '%' . $tuKhoa . '%']
                    ])
                    ->orWhere([
                        ['POST_DETAIL_DESCRIPTION', 'like', '%' . $tuKhoa . '%']
                    ]);
                }
            );
        }

        if ($productHot === true) {
            $query->where([
                ['PRODUCT_HOT', true]
            ]);
        }

        if ($isApiPublic === true) {
            $query->where([
                ['IS_ACTIVE', true]
            ]);
        }
        
        if (!is_null($arrDanhMucSanPhamId) && is_array($arrDanhMucSanPhamId) && count($arrDanhMucSanPhamId) > 0) {
            // Lấy toàn bộ ID danh mục con
            $allCategoryIds = $arrDanhMucSanPhamId;
            $childIds = CategoryP::getAllChildCategoryIds($arrDanhMucSanPhamId);
            $allCategoryIds = array_merge($allCategoryIds, $childIds);

            $query->whereHas('categories', function($query) use ($allCategoryIds) {
                $query->whereIn('CATEGORY_ID', $allCategoryIds);
            });
        }

        if (!is_null($trangThaiHoatDong)) {
            $query->where([
                ['IS_ACTIVE', '=', $trangThaiHoatDong]
            ]);
        }

        if (!blank($boLoc)) {
            switch ($boLoc) {
                case 'default':
                    # code...
                    break;
                case 'gia-tang':
                    $query->orderByRaw('
                                CASE
                                    WHEN PRICE IS NOT NULL THEN PRICE
                                    ELSE 999999999999999
                                END ASC
                            ')
                          ->orderByRaw('
                                CASE
                                    WHEN PRICE_RENT IS NOT NULL THEN PRICE_RENT
                                    ELSE 999999999999999
                                END ASC
                            ');
                    break;
                case 'gia-giam':
                    $query->orderByRaw('
                                CASE
                                    WHEN PRICE IS NOT NULL THEN PRICE
                                    ELSE -1
                                END DESC
                            ')
                          ->orderByRaw('
                                CASE
                                    WHEN PRICE_RENT IS NOT NULL THEN PRICE_RENT
                                    ELSE -1
                                END DESC
                            ');
                    break;
                case 'a-z':
                    $query->orderBy('NAME', 'asc');
                    break;
                case 'z-a':
                    $query->orderBy('NAME', 'desc');
                    break;
                case 'cu-den-moi':
                    $query->orderBy('UPD_DT', 'asc');
                    break;
                case 'moi-den-cu':
                    $query->orderBy('UPD_DT', 'desc');
                    break;
                default:
                    # code...
                    break;
            }
        }

        // Ưu tiên BĐS đang bán (USING) trước, sau đó mới áp dụng các sort khác
        $query->orderByRaw('CASE WHEN STATUS = ? THEN 0 ELSE 1 END', [AppConstant::STATUS_USING]);

        $sortDto = SortRequestDto::fromRequest($request);
        if (!is_null($sortDto) && !is_null($sortDto->fieldName)) {
            $fieldName = $sortDto->fieldName;
            $sortType = $sortDto->sortType;

            switch ($fieldName) {
                case 'STT':
                    $query->orderBy('CRT_DT', strtolower($sortType) == 'desc' ? 'asc' : 'desc');
                    break;
                case 'DANH_MUC_SAN_PHAM':
                    $query->orderBy('CRT_DT', strtolower($sortType));
                    break;
                case 'TEN_SAN_PHAM':
                    $query->orderBy('NAME', strtolower($sortType));
                    break;
                default:
                    # code...
                    break;
            }
        } else {
            $query->orderBy('CRT_DT', 'desc');
        }

        
        $query = $query->paginate($perPage, ['*'], 'page', $page);
        return $query;
    }
}
