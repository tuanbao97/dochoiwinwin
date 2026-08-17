<?php

namespace App\Http\Requests\transaction;

use App\Support\StorefrontVoucher;
use Illuminate\Foundation\Http\FormRequest;

class VoucherQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $codes = StorefrontVoucher::normalizeCodes(array_merge(
            (array) $this->input('DISCOUNT_CODES', $this->input('discount_codes', [])),
            [$this->input('DISCOUNT_CODE', $this->input('discount_code', ''))]
        ));

        $this->merge([
            'DISCOUNT_CODES' => $codes,
            'DISCOUNT_CODE' => implode(',', $codes),
            'EMAIL' => trim((string) $this->input('EMAIL', $this->input('email', ''))),
            'SO_DIEN_THOAI' => trim((string) $this->input('SO_DIEN_THOAI', $this->input('phone', ''))),
        ]);
    }

    public function rules(): array
    {
        return [
            'DISCOUNT_CODES' => ['required', 'array', 'min:1', 'max:2'],
            'DISCOUNT_CODES.*' => ['required', 'string', 'max:255'],
            'EMAIL' => ['nullable', 'email', 'max:1000'],
            'SO_DIEN_THOAI' => ['nullable', 'string', 'max:50'],
            'ITEMS' => ['required', 'array', 'min:1'],
            'ITEMS.*.PRODUCT_ID' => ['required', 'integer'],
            'ITEMS.*.QUANTITY' => ['required', 'integer', 'min:1'],
        ];
    }
}
