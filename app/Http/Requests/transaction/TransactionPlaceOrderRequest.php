<?php

namespace App\Http\Requests\transaction;

use App\Enum\AppConstant;
use App\Exceptions\BadRequestException;
use App\Support\StorefrontCart;
use App\Support\StorefrontVoucher;
use Illuminate\Foundation\Http\FormRequest;

class TransactionPlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $route = $this->route();
        $routePrefix = $route->getPrefix();
        if ($routePrefix === AppConstant::PREFIX_API['API_PUBLIC']) {
            return true;
        }

        return true;
    }

    protected function prepareForValidation()
    {
        $items = $this->input('ITEMS');
        if (! is_array($items) || count($items) === 0) {
            $items = app(StorefrontCart::class)->toOrderItems();
        }

        $normalize = static function ($value): ?string {
            if ($value === null) {
                return null;
            }
            $trimmed = trim((string) $value);

            return $trimmed === '' ? null : $trimmed;
        };

        $phone = $normalize($this->input('SO_DIEN_THOAI', $this->input('phone')));
        if ($phone !== null) {
            $phone = preg_replace('/[\s.\-]/', '', $phone) ?: null;
        }

        $this->merge([
            'HO_TEN' => $normalize($this->input('HO_TEN', $this->input('name'))),
            'SO_DIEN_THOAI' => $phone,
            'EMAIL' => $normalize($this->input('EMAIL', $this->input('email'))),
            'DIA_CHI' => $normalize($this->input('DIA_CHI', $this->input('address'))),
            'GHI_CHU' => $normalize($this->input('GHI_CHU', $this->input('note'))),
            'NHAN_TAI_CUA_HANG' => filter_var(
                $this->input('NHAN_TAI_CUA_HANG', $this->input('pickup', false)),
                FILTER_VALIDATE_BOOLEAN
            ),
            'DISCOUNT_CODE' => $normalize($this->input('DISCOUNT_CODE', $this->input('discount_code'))),
            'DISCOUNT_CODES' => StorefrontVoucher::normalizeCodes(array_merge(
                (array) $this->input('DISCOUNT_CODES', $this->input('discount_codes', [])),
                [$this->input('DISCOUNT_CODE', $this->input('discount_code', ''))]
            )),
            'ITEMS' => $items,
        ]);

        // Client không được quyết định tiền: bỏ mọi field số tiền nếu có gửi kèm.
        foreach ([
            'SHIPPING_FEE', 'shipping_fee',
            'DISCOUNT_AMOUNT', 'discount_amount',
            'SUBTOTAL', 'subtotal',
            'TOTAL', 'total', 'TOTAL_PRICE', 'total_price',
        ] as $moneyField) {
            $this->request->remove($moneyField);
        }
    }

    public function rules(): array
    {
        return [
            'HO_TEN' => [
                'bail',
                'required',
                'string',
                'max:500',
            ],
            'SO_DIEN_THOAI' => [
                'bail',
                'required',
                'string',
                'max:50',
                'regex:/^(0|\+84)(3[2-9]|5[2689]|7[06-9]|8[0-9]|9[0-9])[0-9]{7}$/',
            ],
            'EMAIL' => [
                'bail',
                'nullable',
                'email',
                'max:1000',
            ],
            'DIA_CHI' => [
                'bail',
                'required',
                'string',
                'max:2000',
            ],
            'GHI_CHU' => [
                'bail',
                'nullable',
                'string',
                'max:2000',
            ],
            'NHAN_TAI_CUA_HANG' => [
                'bail',
                'boolean',
            ],
            'DISCOUNT_CODE' => [
                'bail',
                'nullable',
                'string',
                'max:255',
            ],
            'DISCOUNT_CODES' => [
                'bail',
                'nullable',
                'array',
                'max:2',
            ],
            'DISCOUNT_CODES.*' => [
                'bail',
                'string',
                'max:255',
            ],
            'ITEMS' => [
                'bail',
                'required',
                'array',
                'min:1',
                'max:50',
            ],
            'ITEMS.*.PRODUCT_ID' => [
                'bail',
                'required',
                'integer',
            ],
            'ITEMS.*.QUANTITY' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'max:999',
            ],
            // PRICE nếu client gửi cũng bị bỏ qua ở service; không bắt buộc.
            'ITEMS.*.PRICE' => [
                'bail',
                'nullable',
                'numeric',
                'min:0',
            ],
            'ITEMS.*.TEN_SAN_PHAM' => [
                'bail',
                'nullable',
                'string',
            ],
            'ITEMS.*.HINH_ANH' => [
                'bail',
                'nullable',
                'string',
            ],
            'ITEMS.*.HANDLE' => [
                'bail',
                'nullable',
                'string',
            ],
        ];
    }

    public function messages()
    {
        $locale = app()->getLocale();
        $messages = json_decode(file_get_contents(resource_path("lang/{$locale}/validation.json")), true);
        $messagesForThisRequest = json_decode(file_get_contents(resource_path("lang/{$locale}/transaction/validation.json")), true) ?? [];

        return array_merge($messages, $messagesForThisRequest);
    }

    public function attributes()
    {
        $locale = app()->getLocale();
        $attributes = json_decode(file_get_contents(resource_path("lang/{$locale}/transaction/attributes.json")), true) ?? [];

        return $attributes;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new BadRequestException($validator->errors(), 'Đặt hàng thất bại.');
    }

}
