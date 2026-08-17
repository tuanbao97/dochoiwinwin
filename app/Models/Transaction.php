<?php

namespace App\Models;

use App\Enum\AppConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';

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
        'USER_BUY_ID' => null,
        'USER_BUY_EMAIL' => null,
        'USER_BUY_FULLNAME' => null,
        'USER_BUY_PHONE' => null,
        'USER_BUY_ADDRESS' => null,
        'TOTAL_QUANTITY' => 0,
        'TOTAL_PRICE' => 0,
        'USER_BUY_MESSAGE' => null,
        'TRANSACTION_STATUS' => null,
        'PAYMENT_METHOD' => null,
        'PAYMENT_DATE' => null,
        'SAPO_ORDER_ID' => null,
        'SAPO_ORDER_NAME' => null,
        'SAPO_ASSIGNEE_ID' => null,
        'SAPO_ASSIGNEE_NAME' => null,
        'EXPECTED_DELIVERY_DATE' => null,
        'SAPO_SYNC_STATUS' => null,
        'SAPO_SYNC_ATTEMPTS' => 0,
        'SAPO_SYNC_ERROR' => null,
        'SAPO_PAYLOAD' => null,
        'SAPO_SYNCED_AT' => null,
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
        'PAYMENT_DATE' => 'datetime',
        'SAPO_ORDER_ID' => 'integer',
        'SAPO_ASSIGNEE_ID' => 'integer',
        'SAPO_SYNC_ATTEMPTS' => 'integer',
        'SAPO_PAYLOAD' => 'array',
        'SAPO_SYNCED_AT' => 'datetime',
        'EXPECTED_DELIVERY_DATE' => 'datetime',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'TRANSACTION_ID', 'ID')
            ->where('STATUS', AppConstant::STATUS_USING);
    }
}
