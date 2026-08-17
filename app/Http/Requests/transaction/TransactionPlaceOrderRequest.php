<?php

namespace App\Http\Requests\transaction;

use App\Enum\AppConstant;
use App\Exceptions\BadRequestException;
use App\Support\StorefrontVoucher;
use Illuminate\Foundation\Http\FormRequest;

class TransactionPlaceOrderRequest extends FormRequest
{
    private const CART_SESSION_KEY = 'theme_storefront_cart';

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
        if ((! is_array($items) || count($items) === 0) && $this->hasSession() && $this->session()->has(self::CART_SESSION_KEY)) {
            $items = $this->mapCartSessionToItems();
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
            'DISCOUNT_CODE' => $normalize($this->input('DISCOUNT_CODE', $this->input('discount_code'))),
            'DISCOUNT_CODES' => StorefrontVoucher::normalizeCodes(array_merge(
                (array) $this->input('DISCOUNT_CODES', $this->input('discount_codes', [])),
                [$this->input('DISCOUNT_CODE', $this->input('discount_code', ''))]
            )),
            'ITEMS' => $items,
        ]);
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
            ],
            'ITEMS.*.PRICE' => [
                'bail',
                'required',
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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapCartSessionToItems(): array
    {
        $cartLines = $this->session()->get(self::CART_SESSION_KEY, []);
        if (! is_array($cartLines)) {
            return [];
        }

        return array_values(array_map(static function (array $line): array {
            return [
                'PRODUCT_ID' => (int) ($line['variant_id'] ?? $line['PRODUCT_ID'] ?? 0),
                'QUANTITY' => (float) ($line['quantity'] ?? $line['QUANTITY'] ?? 0),
                'PRICE' => (float) ($line['price'] ?? $line['PRICE'] ?? 0),
                'TEN_SAN_PHAM' => $line['title'] ?? $line['TEN_SAN_PHAM'] ?? null,
                'HINH_ANH' => $line['image'] ?? $line['HINH_ANH'] ?? null,
                'HANDLE' => $line['handle'] ?? $line['HANDLE'] ?? null,
            ];
        }, $cartLines));
    }
}
