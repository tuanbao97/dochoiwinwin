<?php

namespace App\Models;

use App\Enum\AppConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    /* Table mapping */
    protected $table = 'product';

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
        'SAPO_ID' => null,
        'SAPO_PAYLOAD' => null,
        'UUID' => null,
        'MA_SAN_PHAM' => null,
        'NAME' => null,
        'TYPE' => null,
        'SHORT_DESCRIPTION' => null,

        'PRICE' => null,
        'PRICE_DISPLAY_TEXT' => null,
        'PRICE_RENT' => null,
        'PRICE_RENT_DEPOSIT_AMOUNT' => null,
        'PRICE_SALE' => null,
        'DISCOUNT' => null,
        
        'TOTAL_VIEWS' => 0,
        'PRODUCT_QUANTITY' => null,
        'PRODUCT_HOT' => null,
        'PRODUCT_VIP' => null,
        'PRODUCT_TAGS' => null,
        'POST_TITLE' => null,
        'POST_DETAIL_DESCRIPTION' => null,
        'KEYWORDS_SEO_WEBSITE' => null,

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
        'SAPO_ID' => 'integer',
        'CRT_DT' => 'datetime',
        'UPD_DT' => 'datetime',
    ];

    public function avatars()
    {
        return $this->belongsToMany(DocumentStorage::class, 'product_document_storage', 'PRODUCT_ID', 'DOCUMENT_STORAGE_ID')
            // Lấy thêm các cột từ bảng trung gian
            ->withPivot(['SORT_ORDER', 'IS_ACTIVE', 'IS_THUMNAIL', 'CRT_DT', 'UPD_DT', 'CRT_ID', 'UPD_ID', 'STATUS', 'TYPE', 'EXTENSION', 'ATTR1'])
            // Ép kiểu dữ liệu trong bảng trung gian
            ->using(ProductDocumentStoragePivot::class)
            // Where thêm điều kiện bảng One To Many còn lại
            ->where(column: [
                ['document_storage.STATUS', '=', AppConstant::STATUS_USING]
            ])
            // Where chính bảng hiện tại
            ->where(column: [
                ['product_document_storage.STATUS', '=', AppConstant::STATUS_USING]
                , ['product_document_storage.ATTR1', '=', 'DANH_SACH_HINH_ANH_DAI_DIEN']
            ])
            ->orderBy('product_document_storage.SORT_ORDER', 'ASC')
            ->orderBy('product_document_storage.ID', 'ASC');
    }

    public function images(): BelongsToMany {
        return $this->belongsToMany(DocumentStorage::class, 'product_document_storage', 'PRODUCT_ID', 'DOCUMENT_STORAGE_ID')
            // Lấy thêm các cột từ bảng trung gian
            ->withPivot(['SORT_ORDER', 'IS_ACTIVE', 'IS_THUMNAIL', 'CRT_DT', 'UPD_DT', 'CRT_ID', 'UPD_ID', 'STATUS', 'TYPE', 'EXTENSION', 'ATTR1'])
            // Ép kiểu dữ liệu trong bảng trung gian
            ->using(ProductDocumentStoragePivot::class)
            // Where thêm điều kiện bảng One To Many còn lại
            ->where(column: [
                ['document_storage.STATUS', '=', AppConstant::STATUS_USING]
            ])
            // Where chính bảng hiện tại
            ->where(column: [
                ['product_document_storage.STATUS', '=', AppConstant::STATUS_USING]
                , ['product_document_storage.ATTR1', '=', 'DANH_SACH_HINH_ANH']
            ])
            ->orderBy('product_document_storage.SORT_ORDER', 'ASC')
            ->orderBy('product_document_storage.ID', 'ASC');
    }

    public function videos(): BelongsToMany {
        return $this->belongsToMany(DocumentStorage::class, 'product_document_storage', 'PRODUCT_ID', 'DOCUMENT_STORAGE_ID')
            // Lấy thêm các cột từ bảng trung gian
            ->withPivot(['SORT_ORDER', 'IS_ACTIVE', 'IS_THUMNAIL', 'CRT_DT', 'UPD_DT', 'CRT_ID', 'UPD_ID', 'STATUS', 'TYPE', 'EXTENSION', 'ATTR1'])
            // Ép kiểu dữ liệu trong bảng trung gian
            ->using(ProductDocumentStoragePivot::class)
            // Where thêm điều kiện bảng One To Many còn lại
            ->where(column: [
                ['document_storage.STATUS', '=', AppConstant::STATUS_USING]
            ])
            // Where chính bảng hiện tại
            ->where(column: [
                ['product_document_storage.STATUS', '=', AppConstant::STATUS_USING]
                , ['product_document_storage.ATTR1', '=', 'DANH_SACH_VIDEO']
            ])
            ->orderBy('product_document_storage.SORT_ORDER', 'ASC')
            ->orderBy('product_document_storage.ID', 'ASC');
    }

    public function files(): BelongsToMany {
        return $this->belongsToMany(DocumentStorage::class, 'product_document_storage', 'PRODUCT_ID', 'DOCUMENT_STORAGE_ID')
            // Lấy thêm các cột từ bảng trung gian
            ->withPivot(['SORT_ORDER', 'IS_ACTIVE', 'IS_THUMNAIL', 'CRT_DT', 'UPD_DT', 'CRT_ID', 'UPD_ID', 'STATUS', 'TYPE', 'EXTENSION', 'ATTR1'])
            // Ép kiểu dữ liệu trong bảng trung gian
            ->using(ProductDocumentStoragePivot::class)
            // Where thêm điều kiện bảng One To Many còn lại
            ->where(column: [
                ['document_storage.STATUS', '=', AppConstant::STATUS_USING]
            ])
            // Where chính bảng hiện tại
            ->where(column: [
                ['product_document_storage.STATUS', '=', AppConstant::STATUS_USING]
                , ['product_document_storage.ATTR1', '=', 'DANH_SACH_FILE_DINH_KEM']
            ])
            ->orderBy('product_document_storage.SORT_ORDER', 'ASC')
            ->orderBy('product_document_storage.ID', 'ASC');
    }

    public function categories()
    {
        return $this->belongsToMany(CategoryP::class, 'product_category', 'PRODUCT_ID', 'CATEGORY_ID')
            // Lấy thêm các cột từ bảng trung gian
            ->withPivot(['SORT_ORDER', 'IS_ACTIVE', 'CRT_DT', 'UPD_DT', 'CRT_ID', 'UPD_ID', 'STATUS'])
            // Ép kiểu dữ liệu trong bảng trung gian
            ->using(ProductCategoryPivot::class)
            // Where thêm điều kiện bảng One To Many còn lại
            ->where(column: [
                ['category_p.STATUS', '=', AppConstant::STATUS_USING]
            ])
            // Where chính bảng hiện tại
            ->where([
                ['product_category.STATUS', '=', AppConstant::STATUS_USING]
            ]);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'PRODUCT_ID', 'ID')
            // Where thêm điều kiện bảng
            ->where([
                ['product_variant.STATUS', '=', AppConstant::STATUS_USING]
            ]);
    }
}
