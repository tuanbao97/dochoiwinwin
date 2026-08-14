<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentStorage extends Model
{
    use HasFactory;

    /* Table mapping */
    protected $table = 'document_storage';
    
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
        'SAPO_IMAGE_ID' => null,
        'SOURCE_URL' => null,
        'NAME' => null,
        'ORIGINAL_NAME' => null,
        'EXTENSION' => null,
        'PATH' => null,
        'DIRECTORY' => null,
        'SIZE' => null,
        'MD5' => null,
        'TYPE_FILE' => null,
        'DESCRIPTION' => null,
        'CRT_DT' => null,
        'UPD_DT' => null,
        'CRT_ID' => null,
        'UPD_ID' => null,
        'CRT_NAME' => null,
        'UPD_NAME' => null,
        'STATUS' => 'USING'
    ];

    /* Định nghĩa kiểu dữ liệu các attributes */
    protected $casts = [
        'SAPO_IMAGE_ID' => 'integer',
        'CRT_DT' => 'datetime',
        'UPD_DT' => 'datetime',
    ];

}
