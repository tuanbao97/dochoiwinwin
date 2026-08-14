<?php

namespace App\Http\Requests\product;

use App\Enum\AppConstant;
use App\Enum\PermissionEnum;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Rules\CheckNotExistsFieldRule;
use App\Service\SapoService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ProductDetailRequest extends FormRequest
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
            if ($permission->CODE === PermissionEnum::QL_SAN_PHAM_CHI_TIET->value) {
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
        $id = $this->route('ID');
        $this->merge(['ID' => $id]);
    }
    
    /* Set rule validate cho request này */
    public function rules(): array
    {
        $rules = [
            'ID' => [
                'bail'
                , 'required'
                , 'integer'
            ]
        ];

        // Public + Sapo: cho phép ID từ Sapo (không bắt buộc có trong DB local)
        $rules['ID'][] = function ($attribute, $value, $fail) {
            $routePrefix = (string) ($this->route()?->getPrefix() ?? '');
            $isPublic = $routePrefix === AppConstant::PREFIX_API['API_PUBLIC'];
            if ($isPublic) {
                try {
                    if (app(SapoService::class)->isEnabled()) {
                        return;
                    }
                } catch (\Throwable) {
                    // Fall through to local DB check
                }
            }

            $exists = DB::table('product')
                ->where('ID', $value)
                ->whereIn('STATUS', ['USING', 'SOLD'])
                ->exists();

            if (! $exists) {
                $locale = app()->getLocale();
                $messages = json_decode(file_get_contents(resource_path("lang/{$locale}/validation.json")), true);
                $fail($messages['does_not_exists'] ?? ':attribute không tồn tại.');
            }
        };

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
        ];

        /* Merge message from locale and for this request */
        $attributes = array_merge($attributes, $attributesForThisRequest);

        return $attributes;
    }
    
    /* Override phương thức failedValidation để custom lại lỗi khi validate thất bại */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Kiểm tra mã lỗi là loại gì để throw exception cho chính xác loại 404 hay 400 ...
        $idFailedRules = $validator->failed()['ID'] ?? [];
        if (!empty($idFailedRules)) {
            $collectionIdFailedRules = collect($idFailedRules);
            $idFailedRuleNotFound = $collectionIdFailedRules->filter(function($value, $key) {
                return $key == CheckNotExistsFieldRule::class;
            });
            if ($idFailedRuleNotFound->isNotEmpty()) {
                throw new NotFoundException($validator->errors(), 'Sản phẩm không tồn tại.');
            }
        }
        
        // Throw bad request custom
        throw new BadRequestException($validator->errors(), 'Truy vấn thất bại.');
    }

}
