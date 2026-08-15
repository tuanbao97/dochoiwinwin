<?php

namespace App\Http\Requests\product;

use App\Enum\AppConstant;
use App\Enum\PermissionEnum;
use App\Exceptions\BadRequestException;
use App\Rules\CheckNotExistsFieldRule;
use App\Rules\LessThanOrEqualRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ProductSaveRequest extends FormRequest
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
            if ($permission->CODE === PermissionEnum::QL_SAN_PHAM_CHINH_SUA->value) {
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
        // Tương thích payload cũ chỉ gửi một DANH_MUC_SAN_PHAM.
        $categories = $this->input('DANH_MUC_SAN_PHAMS');
        $legacyCategory = $this->input('DANH_MUC_SAN_PHAM');

        if (!is_array($categories) && is_array($legacyCategory) && !empty($legacyCategory['ID'])) {
            $categories = [$legacyCategory];
        }

        // Merge các query param từ query param vào input array
        $this->merge([
            'DANH_MUC_SAN_PHAMS' => $categories,
        ]);
    }

    /* Set rule validate cho request này */
    public function rules(): array
    {
        $rules = [
            'ID' => [
                'bail'
                , 'nullable'
                , 'integer'
            ]


            , 'DANH_SACH_HINH_ANH_DAI_DIEN' => [
                'bail'
                , 'required'
                , 'array' // Bắt buộc phải là 1 mảng/ object trong laravel
            ]
            , 'DANH_SACH_HINH_ANH_DAI_DIEN.*.ID' => [
                'bail'
                , 'required'
                , 'integer'
            ]

            , 'DANH_SACH_HINH_ANH' => [
                'bail'
                , 'nullable'
                , 'array' // Bắt buộc phải là 1 mảng/ object trong laravel
            ]
            , 'DANH_SACH_HINH_ANH.*.ID' => [
                'bail'
                , 'required'
                , 'integer'
            ]

            , 'DANH_SACH_VIDEO' => [
                'bail'
                , 'nullable'
                , 'array' // Bắt buộc phải là 1 mảng/ object trong laravel
            ]
            , 'DANH_SACH_VIDEO.*.ID' => [
                'bail'
                , 'required'
                , 'integer'
            ]

            , 'DANH_SACH_FILE_DINH_KEM' => [
                'bail'
                , 'nullable'
                , 'array' // Bắt buộc phải là 1 mảng/ object trong laravel
            ]
            , 'DANH_SACH_FILE_DINH_KEM.*.ID' => [
                'bail'
                , 'required'
                , 'integer'
            ]

            , 'DANH_MUC_SAN_PHAMS' => [
                'bail'
                , 'required'
                , 'array'
                , 'min:1'
            ]
            , 'DANH_MUC_SAN_PHAMS.*.ID' => [
                'bail'
                , 'required'
                , 'integer'
                , 'distinct'
            ]
            , 'TEN_SAN_PHAM' => [
                'bail'
                , 'required'
            ]
            , 'MA_SAN_PHAM' => [
                'bail'
                , 'nullable'
                , 'string'
                , 'max:100'
            ]
            , 'KEYWORDS_SEO_WEBSITE' => [
                'bail'
                , 'required'
                , 'string'
                , 'max:500'
            ]

            , 'IS_GIA_CA_LIEN_HE' => [
                'bail'
                , 'required'
                , 'boolean'
            ]
            , 'GIA_CA' => [
                'bail'
                , 'nullable'
                , 'string'
                , 'required_if:IS_GIA_CA_LIEN_HE,false'
            ]
            , 'GIA_GOC' => [
                'bail'
                , 'nullable'
                , 'numeric'
                , 'min:0'
            ]
            , 'GIA_HIEN_THI' => [
                'bail'
                , 'nullable'
                , 'string'
                , 'max:1000'
            ]
            , 'DAC_DIEM' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'DAC_DIEM_ONLY_TEXT' => [
                'bail'
                , 'nullable'
                , 'string'
            ]
            , 'MO_TA_CHI_TIET' => [
                'bail'
                , 'required'
            ]
            , 'MO_TA_CHI_TIET_ONLY_TEXT' => [
                'bail'
                , 'required'
            ]

            , 'TRANG_THAI_HOAT_DONG' => [
                'bail'
                , 'required'
                , 'boolean'
            ]

            , 'SAN_PHAM_NOI_BAT' => [
                'bail'
                , 'nullable'
                , 'boolean'
            ]

            , 'SAN_PHAM_VIP' => [
                'bail'
                , 'nullable'
                , 'boolean'
            ]
        ];

        // Check tồn tại id sản phẩm (cho phép cả USING và SOLD)
        $id = $this->input('ID', null);
        if ($id) {
            $rules['ID'][] = function($attribute, $value, $fail) use ($id) {
                if (!DB::table('product')->where('ID', $id)->whereIn('STATUS', ['USING', 'SOLD'])->exists()) {
                    $fail('Id sản phẩm không tồn tại.');
                }
            };
        }
        
        // Check tồn tại danh sách hình ảnh đại diện
        $danhSachHinhAnhDaiDienUpload = $this->input('DANH_SACH_HINH_ANH_DAI_DIEN', null);
        if (!is_null($danhSachHinhAnhDaiDienUpload) && count($danhSachHinhAnhDaiDienUpload) > 0) {
            foreach($danhSachHinhAnhDaiDienUpload as $index => $value) {
                // Check tồn tại hình ảnh upload
                $documentStorageId = $value['ID'];
                $rules['DANH_SACH_HINH_ANH_DAI_DIEN'][] = new CheckNotExistsFieldRule('document_storage', 'ID', $documentStorageId, 'USING');
            }
        }

        // Check tồn tại danh sách hình ảnh upload
        $danhSachHinhAnhUpload = $this->input('DANH_SACH_HINH_ANH', null);
        if (!is_null($danhSachHinhAnhUpload) && count($danhSachHinhAnhUpload) > 0) {
            foreach($danhSachHinhAnhUpload as $index => $value) {
                // Check tồn tại hình ảnh upload
                $documentStorageId = $value['ID'];
                $rules['DANH_SACH_HINH_ANH'][] = new CheckNotExistsFieldRule('document_storage', 'ID', $documentStorageId, 'USING');
            }
        }

        // Check tồn tại danh sách video upload
        $danhSachVideoUpload = $this->input('DANH_SACH_VIDEO', null);
        if (!is_null($danhSachVideoUpload) && count($danhSachVideoUpload) > 0) {
            foreach($danhSachVideoUpload as $index => $value) {
                // Check tồn tại hình ảnh upload
                $documentStorageId = $value['ID'];
                $rules['DANH_SACH_VIDEO'][] = new CheckNotExistsFieldRule('document_storage', 'ID', $documentStorageId, 'USING');
            }
        }

        // Check tồn tại danh sách file đính kèm upload
        $danhSachFileDinhKemUpload = $this->input('DANH_SACH_FILE_DINH_KEM', null);
        if (!is_null($danhSachFileDinhKemUpload) && count($danhSachFileDinhKemUpload) > 0) {
            foreach($danhSachFileDinhKemUpload as $index => $value) {
                // Check tồn tại hình ảnh upload
                $documentStorageId = $value['ID'];
                $rules['DANH_SACH_FILE_DINH_KEM'][] = new CheckNotExistsFieldRule('document_storage', 'ID', $documentStorageId, 'USING');
            }
        }

        // Check tồn tại danh mục sản phẩm
        $danhMucSanPhams = $this->input('DANH_MUC_SAN_PHAMS', []);
        if (is_array($danhMucSanPhams)) {
            foreach ($danhMucSanPhams as $index => $danhMucSanPham) {
                $danhMucSanPhamId = is_array($danhMucSanPham) ? ($danhMucSanPham['ID'] ?? null) : null;
                if ($danhMucSanPhamId !== null) {
                    $rules["DANH_MUC_SAN_PHAMS.{$index}.ID"][] =
                        new CheckNotExistsFieldRule('category_p', 'ID', $danhMucSanPhamId, 'USING');
                }
            }
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
            'IS_GIA_CA_LIEN_HE' => 'Giá liên hệ',
            'GIA_CA' => 'Giá cả',
            'GIA_GOC' => 'Giá gốc',
            'GIA_HIEN_THI' => 'Giá hiển thị',
            'KEYWORDS_SEO_WEBSITE' => 'Từ khóa SEO',
            'MA_SAN_PHAM' => 'Mã sản phẩm',
        ];

        /* Merge message from locale and for this request */
        $attributes = array_merge($attributes, $attributesForThisRequest);

        return $attributes;
    }
    
    /* Override phương thức failedValidation để custom lại lỗi khi validate thất bại */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Throw bad request custom
        throw new BadRequestException($validator->errors(), 'Lưu thất bại.');
    }

}
