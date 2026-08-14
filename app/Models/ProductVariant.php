<?php

namespace App\Models;

use App\Enum\AppConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    /* Table mapping */
    protected $table = 'product_variant';

    /* Khóa chính */
    protected $primaryKey = 'ID';

    /* Kiểu dữ liệu của khóa chính */
    protected $keyType = 'integer';

    /* Khóa chính này là kiểu số tự tăng */
    public $incrementing = true;

    /* Lọc các column sẽ sử dụng */
    // protected $fillable = [];

    /* Lọc các column không sử dụng */
    protected $guarded = [];

    /* Ẩn các field khỏi arrays */
    protected $hidden = [];

    /* Set giá trị create_at và update_at khi thêm mới/ cập nhật dữ liệu */
    const CREATED_AT = 'CRT_DT';
    const UPDATED_AT = 'UPD_DT';
    public $timestamps = true;

    /* Thiêt lập giá trịm mặc định cho instance */
    protected $attributes = [
        'ID' => null,
        'SAPO_VARIANT_ID' => null,
        'PRODUCT_ID' => null,
        'PRODUCT_STATUS' => null,
        'PRODUCT_COLOR' => null,
        'PRODUCT_STORAGE' => null,
        'PRODUCT_IMAGE_ID' => null,
        'IS_CONTACT_PRICE' => false,
        'PRODUCT_PRICE' => null,
        'PRODUCT_ORIGINAL_PRICE' => null,
        'IS_IN_STOCK' => true,

        'CRT_DT' => null,
        'UPD_DT' => null,
        'CRT_ID' => null,
        'UPD_ID' => null,
        'CRT_NAME' => null,
        'UPD_NAME' => null,
        'STATUS' => 'USING',
        'IS_ACTIVE' => true
    ];

    /* Định nghĩa kiểu dữ liệu các attributes */
    protected $casts = [
        'IS_ACTIVE' => 'boolean',
        'IS_CONTACT_PRICE' => 'boolean',
        'IS_IN_STOCK' => 'boolean',
        'SAPO_VARIANT_ID' => 'integer',
        'CRT_DT' => 'datetime',
        'UPD_DT' => 'datetime',
    ];

    public function productImage() {
        return $this->belongsTo(DocumentStorage::class, 'PRODUCT_IMAGE_ID', 'ID')
            // Where thêm điều kiện bảng
            ->where([
                ['document_storage.STATUS', '=', AppConstant::STATUS_USING]
            ]);
    }

}
