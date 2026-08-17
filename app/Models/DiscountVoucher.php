<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountVoucher extends Model
{
    protected $table = 'discount_voucher';

    protected $primaryKey = 'ID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];

    public const CREATED_AT = 'CRT_DT';

    public const UPDATED_AT = 'UPD_DT';

    protected $casts = [
        'SAPO_PRICE_RULE_ID' => 'integer',
        'SAPO_DISCOUNT_CODE_ID' => 'integer',
        'DISCOUNT_VALUE' => 'integer',
        'MAX_DISCOUNT_AMOUNT' => 'integer',
        'MIN_SUBTOTAL' => 'integer',
        'USAGE_LIMIT' => 'integer',
        'ONCE_PER_CUSTOMER' => 'boolean',
        'STARTS_AT' => 'datetime',
        'ENDS_AT' => 'datetime',
        'SAPO_PAYLOAD' => 'array',
        'IS_ACTIVE' => 'boolean',
        'CRT_DT' => 'datetime',
        'UPD_DT' => 'datetime',
    ];
}
