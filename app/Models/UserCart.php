<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCart extends Model
{
    protected $table = 'user_cart';

    protected $primaryKey = 'ID';

    protected $guarded = [];

    const CREATED_AT = 'CRT_DT';

    const UPDATED_AT = 'UPD_DT';

    protected $casts = [
        'USER_ID' => 'integer',
        'VARIANT_ID' => 'integer',
        'PRODUCT_ID' => 'integer',
        'SAPO_VARIANT_ID' => 'integer',
        'QUANTITY' => 'integer',
        'PRICE' => 'integer',
        'LINE_PRICE' => 'integer',
        'CATEGORY_ID' => 'integer',
        'STOCK' => 'integer',
        'POSITION' => 'integer',
        'CRT_DT' => 'datetime',
        'UPD_DT' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'USER_ID', 'ID');
    }

    /**
     * Dòng giỏ dùng cho theme / checkout (cùng shape với giỏ cũ trên session).
     *
     * @return array<string, mixed>
     */
    public function toCartLine(): array
    {
        $quantity = max(0, (int) $this->QUANTITY);
        $price = max(0, (int) $this->PRICE);

        return [
            'variant_id' => (int) $this->VARIANT_ID,
            'product_id' => (int) ($this->PRODUCT_ID ?: 0),
            'sapo_variant_id' => $this->SAPO_VARIANT_ID ? (int) $this->SAPO_VARIANT_ID : null,
            'title' => (string) ($this->TITLE ?? ''),
            'variant_title' => (string) ($this->VARIANT_TITLE ?: 'Mặc định'),
            'quantity' => $quantity,
            'price' => $price,
            'line_price' => $quantity * $price,
            'image' => (string) ($this->IMAGE ?? ''),
            'handle' => (string) ($this->HANDLE ?? ''),
            'category_id' => (int) ($this->CATEGORY_ID ?: 0),
            'stock' => (int) ($this->STOCK ?: 0),
        ];
    }
}
