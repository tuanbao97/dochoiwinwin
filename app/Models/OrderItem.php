<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order';

    protected $primaryKey = 'ID';

    protected $keyType = 'integer';

    public $incrementing = true;

    protected $guarded = [];

    protected $hidden = [];

    const CREATED_AT = 'CRT_DT';
    const UPDATED_AT = 'UPD_DT';
    public $timestamps = true;

    protected $attributes = [
        'ID' => null,
        'TRANSACTION_ID' => null,
        'PRODUCT_ID' => null,
        'PRODUCT_VARIANT_ID' => null,
        'SAPO_VARIANT_ID' => null,
        'QUANTITY' => 0,
        'PRICE' => 0,
        'CRT_DT' => null,
        'UPD_DT' => null,
        'CRT_ID' => null,
        'UPD_ID' => null,
        'CRT_NAME' => null,
        'UPD_NAME' => null,
        'STATUS' => 'USING',
        'IS_ACTIVE' => true,
    ];

    protected $casts = [
        'IS_ACTIVE' => 'boolean',
        'CRT_DT' => 'datetime',
        'UPD_DT' => 'datetime',
        'PRODUCT_VARIANT_ID' => 'integer',
        'SAPO_VARIANT_ID' => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'TRANSACTION_ID', 'ID');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'PRODUCT_ID', 'ID');
    }
}
