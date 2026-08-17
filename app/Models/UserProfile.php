<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    /* Table mapping */
    protected $table = 'user_profile';
    
    /* Khóa chính */
    protected $primaryKey = 'ID';
    
    /* Kiểu dữ liệu của khóa chính */
    protected $keyType = 'integer';
    
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
        'USER_ID' => null,
        'MOBILE' => null,
        'ADDRESS' => null,
        'IS_DEFAULT' => false,
        'DESCRIPTION' => null,
        'AVATAR_ID' => null,
        'CRT_DT' => null,
        'UPD_DT' => null,
        'CRT_ID' => null,
        'UPD_ID' => null,
        'CRT_NAME' => null,
        'UPD_NAME' => null,
        'STATUS' => 'USING',
        'IS_ACTIVE' => true,
    ];
    
    /* Định nghĩa kiểu dữ liệu các attributes */
    protected $casts = [
        'IS_ACTIVE' => 'boolean',
        'CRT_DT' => 'datetime',
        'UPD_DT' => 'datetime',
    ];

}
