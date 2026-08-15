<?php

namespace App\Http\Requests\setting;

use App\Enum\AppConstant;
use App\Enum\PermissionEnum;
use App\Exceptions\BadRequestException;
use App\Rules\CheckNotExistsFieldRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingWebSaveRequest extends FormRequest
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
            if ($permission->CODE === PermissionEnum::QL_CAI_DAT_CHINH_SUA->value) {
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
        // Merge các query param từ query param vào input array
        $this->merge([]);
    }

    /* Set rule validate cho request này */
    public function rules(): array
    {
        $rules = [
            'SETTING_TEN_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_EMAIL' => [
                'bail',
                'required',
                'email',
            ],
            'SETTING_MA_SO_THUE' => [
                'bail',
                'nullable',
                'string',
            ],
            'SETTING_THOI_GIAN_LAM_VIEC' => [
                'bail',
                'required',
                'string'
            ],
            'SETTING_MO_TA_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DIA_CHI_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DUONG_DAN_GG_MAP_CUA_HANG' => [
                'bail',
                'required',
                'string',
                'url',
            ],
            'SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG' => [
                'bail',
                'required',
                'string',
                'url',
            ],
            'SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DUONG_DAN_SO_ZALO_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DUONG_DAN_TIKTOK_CUA_HANG' => [
                'bail',
                'nullable',
                'string',
            ],
            'SETTING_DUONG_DAN_YOUTUBE_CUA_HANG' => [
                'bail',
                'nullable',
                'string',
            ],
            'SETTING_GIOI_THIEU_CUA_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_GIOI_THIEU_CUA_HANG_ONLY_TEXT' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_CAM_KET_BAN_HANG' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_CAM_KET_BAN_HANG_ONLY_TEXT' => [
                'bail',
                'required',
                'string',
            ],

            'SETTING_DANH_SACH_HOTLINE' => [
                'bail',
                'required',
                'array', // Bắt buộc phải là 1 mảng/ object trong laravel
            ],
            'SETTING_DANH_SACH_HOTLINE.*.LOAI' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DANH_SACH_HOTLINE.*.SDT' => [
                'bail',
                'required',
                'string',
            ],
            'SETTING_DANH_SACH_HOTLINE.*.TEN_CHU_SDT' => [
                'bail',
                'required',
                'string',
            ],
        ];


        return $rules;
    }

    /* Override phương thức messages để custom message lỗi trả về như mong muốn */
    public function messages()
    {
        $locale = app()->getLocale();
        $messages = json_decode(file_get_contents(resource_path("lang/{$locale}/validation.json")), true);

        /* Message for this request */
        $messagesForThisRequest = json_decode(file_get_contents(resource_path("lang/{$locale}/setting/validation.json")), true);

        /* Merge message from locale and for this request */
        $messages = array_merge($messages, $messagesForThisRequest);

        return $messages;
    }
    
    /* Override phương thức attributes để change label thành tên hiển thị lỗi như mong muốn */
    public function attributes()
    {
        $locale = app()->getLocale();
        $attributes = json_decode(file_get_contents(resource_path("lang/{$locale}/setting/attributes.json")), true);
        
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
        throw new BadRequestException($validator->errors(), 'Lưu cấu hình website thất bại.');
    }

}
