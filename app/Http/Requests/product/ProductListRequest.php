<?php

namespace App\Http\Requests\product;

use App\Enum\AppConstant;
use App\Enum\PermissionEnum;
use App\Exceptions\BadRequestException;
use App\Rules\CheckNotExistsFieldRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductListRequest extends FormRequest
{
    /* Xác định xem người dùng có được phép thực hiện yêu cầu này hay không. */
    public function authorize(): bool
    {
        $route = $this->route();
        $routePrefix = $route->getPrefix();
        if ($routePrefix === AppConstant::PREFIX_API['API_PUBLIC']) return true; // Nếu là /api/public thì không cần check authorziration

        $currUser = $this->user();
        $permissions = $currUser->PERMISSIONS;
        foreach ($permissions as $index => $permission) {
            if ($permission->CODE === PermissionEnum::QL_SAN_PHAM_DANH_SACH->value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Chuẩn hóa dự liệu trước khi validate
     */
    protected function prepareForValidation()
    {
        $arrDanhMucSanPham = $this->query('DANH_MUC_SAN_PHAM_ID', null);
        if (!is_null($arrDanhMucSanPham) && count($arrDanhMucSanPham) > 0) {
            $arrDanhMucSanPham = array_filter($arrDanhMucSanPham, function($item) {
                return $item !== 'all';
            });
        }

        $arrDanhSachSanPhamId = $this->query('DANH_SACH_SAN_PHAM_ID', null);
        if (!is_null($arrDanhSachSanPhamId) && !is_array($arrDanhSachSanPhamId)) {
            $arrDanhSachSanPhamId = [$arrDanhSachSanPhamId];
        }

        // Sort
        $sort = $this->query('SORT', null);

        // Merge các query param từ query param vào input array
        $this->merge([
            'DRAW' => $this->query('draw', 1)
            , 'TU_KHOA' => $this->query('TU_KHOA', null)
            , 'TRANG_THAI_HOAT_DONG' => $this->query('TRANG_THAI_HOAT_DONG', null) !== null
                ? filter_var($this->query('TRANG_THAI_HOAT_DONG'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                : null
            , 'TRANG_THAI' => $this->query('TRANG_THAI', 'all') != 'all' ? $this->query('TRANG_THAI') : null
            , 'DANH_MUC_SAN_PHAM_ID' => $arrDanhMucSanPham
            , 'DANH_SACH_SAN_PHAM_ID' => $arrDanhSachSanPhamId
            , 'BO_LOC' => $this->query('BO_LOC', null)
            
            , 'MUC_GIA' => $this->query('MUC_GIA') ?? null
            , 'TINH_THANH_PHO' => $this->query('TINH_THANH_PHO', 'all') != 'all' ? $this->query('TINH_THANH_PHO') : null
            , 'QUAN_HUYEN' => $this->query('QUAN_HUYEN', 'all') != 'all' ? $this->query('QUAN_HUYEN') : null
            , 'PHUONG_XA_THI_TRAN' => $this->query('PHUONG_XA_THI_TRAN', 'all') != 'all' ? $this->query('PHUONG_XA_THI_TRAN') : null
            , 'PROVINCE_CODE' => $this->query('PROVINCE_CODE', null)
            , 'DISTRICT_CODE' => $this->query('DISTRICT_CODE', null)
            , 'WARD_CODE' => $this->query('WARD_CODE', null)
            , 'TINH_THANH_CODE' => $this->query('TINH_THANH_CODE', 'all') != 'all' ? $this->query('TINH_THANH_CODE') : null
            , 'QUAN_HUYEN_CODE' => $this->query('QUAN_HUYEN_CODE', 'all') != 'all' ? $this->query('QUAN_HUYEN_CODE') : null
            , 'PHUONG_XA_CODE' => $this->query('PHUONG_XA_CODE', 'all') != 'all' ? $this->query('PHUONG_XA_CODE') : null
            , 'SORT' => $sort
        ]);
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'DANH_MUC_SAN_PHAM_ID' => [
                'bail',
                'nullable',
                'array'
            ]
            ,'DANH_MUC_SAN_PHAM_ID.*' => [
                'bail',
                'required',
                'integer'
            ]
            , 'DANH_SACH_SAN_PHAM_ID' => [
                'bail',
                'nullable',
                'array',
                'max:50',
            ]
            , 'DANH_SACH_SAN_PHAM_ID.*' => [
                'bail',
                'required',
                'integer',
                'min:1',
            ]
            , 'TRANG_THAI_HOAT_DONG' => [
                'bail'
                , 'nullable'
                , 'boolean'
            ]
            , 'TRANG_THAI' => [
                'bail'
                , 'nullable'
                , 'string'
                , Rule::in([AppConstant::STATUS_USING, AppConstant::STATUS_SOLD])
            ]
            , 'BO_LOC' => [
                'bail'
                , 'nullable'
                , 'string'
                , Rule::in(AppConstant::DANH_SACH_BO_LOC_TIM_KIEM)
            ]

            , 'MUC_GIA' => [
                'bail'
                , 'nullable'
                , 'array'
            ]
            , 'MUC_GIA.*.MIN_VALUE' => [
                'bail'
                , 'nullable'
                , 'numeric'
            ]
            , 'MUC_GIA.*.MAX_VALUE' => [
                'bail'
                , 'nullable'
                , 'numeric'
            ]
            , 'MUC_GIA.*.MIN_INCLUSIVE' => [
                'bail'
                , 'nullable'
                , 'boolean'
            ]
            , 'MUC_GIA.*.MAX_INCLUSIVE' => [
                'bail'
                , 'nullable'
                , 'boolean'
            ]
            , 'TINH_THANH_PHO' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'QUAN_HUYEN' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'PHUONG_XA_THI_TRAN' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'PROVINCE_CODE' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'DISTRICT_CODE' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'WARD_CODE' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'TINH_THANH_CODE' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'QUAN_HUYEN_CODE' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'PHUONG_XA_CODE' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
        ];

        // Check tồn tại danh mục sản phẩm (bỏ khi public + Sapo: ID có thể là collection_id)
        $arrDanhMucSanPhamId = $this->query('DANH_MUC_SAN_PHAM_ID', null);
        $isPublic = ((string) ($this->route()?->getPrefix() ?? '')) === AppConstant::PREFIX_API['API_PUBLIC'];
        $skipLocalCategoryCheck = false;
        if ($isPublic) {
            try {
                $skipLocalCategoryCheck = app(\App\Service\SapoService::class)->isEnabled();
            } catch (\Throwable) {
                $skipLocalCategoryCheck = false;
            }
        }
        if (! $skipLocalCategoryCheck && ! is_null($arrDanhMucSanPhamId) && is_array($arrDanhMucSanPhamId)) {
            foreach ($arrDanhMucSanPhamId as $danhMucSanPhamId) {
                if (is_numeric($danhMucSanPhamId)) {
                    $rules['DANH_MUC_SAN_PHAM_ID'][] = new CheckNotExistsFieldRule('category_p', 'ID', $danhMucSanPhamId, 'USING');
                }
            }
        }

        // Check tồn tại tỉnh thành phố
        $tinhThanhPho = $this->input('TINH_THANH_PHO', null);
        if (!is_null($tinhThanhPho)) {
            $rules['TINH_THANH_PHO'][] = new CheckNotExistsFieldRule('province', 'CODE', $tinhThanhPho, 'USING');
        }

        // Check tồn tại quận huyện
        $quanHuyen = $this->input('QUAN_HUYEN', null);
        if (!is_null($quanHuyen)) {
            $rules['QUAN_HUYEN'][] = new CheckNotExistsFieldRule('district', 'CODE', $quanHuyen, 'USING');
        }

        // Check tồn tại phường xã thị trấn
        $phuongXaThiTran = $this->input('PHUONG_XA_THI_TRAN', null);
        if (!is_null($phuongXaThiTran)) {
            $rules['PHUONG_XA_THI_TRAN'][] = new CheckNotExistsFieldRule('ward', 'CODE', $phuongXaThiTran, 'USING');
        }

        // Check tồn tại PROVINCE_CODE
        $provinceCode = $this->input('PROVINCE_CODE', null);
        if (!is_null($provinceCode)) {
            $rules['PROVINCE_CODE'][] = new CheckNotExistsFieldRule('province', 'CODE', $provinceCode, 'USING');
        }

        // Check tồn tại DISTRICT_CODE
        $districtCode = $this->input('DISTRICT_CODE', null);
        if (!is_null($districtCode)) {
            $rules['DISTRICT_CODE'][] = new CheckNotExistsFieldRule('district', 'CODE', $districtCode, 'USING');
        }

        // Check tồn tại WARD_CODE
        $wardCode = $this->input('WARD_CODE', null);
        if (!is_null($wardCode)) {
            $rules['WARD_CODE'][] = new CheckNotExistsFieldRule('ward', 'CODE', $wardCode, 'USING');
        }

        // Check tồn tại TINH_THANH_CODE
        $tinhThanhCode = $this->input('TINH_THANH_CODE', null);
        if (!is_null($tinhThanhCode)) {
            $rules['TINH_THANH_CODE'][] = new CheckNotExistsFieldRule('province', 'CODE', $tinhThanhCode, 'USING');
        }

        // Check tồn tại QUAN_HUYEN_CODE
        $quanHuyenCode = $this->input('QUAN_HUYEN_CODE', null);
        if (!is_null($quanHuyenCode)) {
            $rules['QUAN_HUYEN_CODE'][] = new CheckNotExistsFieldRule('district', 'CODE', $quanHuyenCode, 'USING');
        }

        // Check tồn tại PHUONG_XA_CODE
        $phuongXaCode = $this->input('PHUONG_XA_CODE', null);
        if (!is_null($phuongXaCode)) {
            $rules['PHUONG_XA_CODE'][] = new CheckNotExistsFieldRule('ward', 'CODE', $phuongXaCode, 'USING');
        }
        
        return $rules;
    }

    /* Override phương thức messages để custom message lỗi trả về như mong muốn */
    public function messages()
    {
        $locale = app()->getLocale();
        $messages = json_decode(file_get_contents(resource_path("lang/{$locale}/validation.json")), true);

        /* Message for this request */
        $messagesForThisRequest = json_decode(file_get_contents(resource_path("lang/{$locale}/product/validation.json")), true);
        
        /* Message for this request */
        $messagesForThisRequest = [
            
        ];
        
        /* Merge message from locale and for this request */
        $messages = array_merge($messages, $messagesForThisRequest);
        
        return $messages;
    }

    /* Override phương thức attributes để change label thành tên hiển thị lỗi như mong muốn */
    public function attributes()
    {
        $locale = app()->getLocale();
        $attributes = json_decode(file_get_contents(resource_path("lang/{$locale}/product/attributes.json")), true);
        
        /* Attributes for this request */
        $attributesForThisRequest = [
            'other' => $this->ID,
        ];
        
        /* Merge message from locale and for this request */
        $attributes = array_merge($attributes, $attributesForThisRequest);
        
        return $attributes;
    }

    /* Override phương thức failedValidation để custom lại lỗi khi validate thất bại */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Throw bad request custom
        throw new BadRequestException($validator->errors(), 'Truy vấn thất bại.');
    }

}
