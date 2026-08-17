<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\AppConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /* Table mapping */
    protected $table = 'user';
    
    /* Khóa chính */
    protected $primaryKey = 'ID';

    /* Kiểu dữ liệu của khóa chính */
    protected $keyType = 'integer';

    /* Khóa chính này là kiểu số tự tăng */
    public $incrementing = true;

    /* Lọc các column sẽ sử dụng */
   //  protected $fillable = [];

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
        'RESET_DATE' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }

    /* Override phương thức của Passport để tìm kiếm theo username */
    public function findForPassport($username)
    {
        return $this->where('EMAIL', $username)
                    ->where('STATUS', '!=', AppConstant::STATUS_DELETED)
                    ->first();
    }

    public function permissions() {
        return DB::table('role_permission as rp')
                ->join('role as r', function($join) {
                    $join->on('rp.role_id', '=', 'r.id')
                         ->where('r.IS_ACTIVE', true); // Điều kiện IS_ACTIVE cho bảng role
                })
                ->join('permission as p', function($join) {
                    $join->on('p.CODE', '=', 'rp.PERMISSION_CODE')
                         ->where('p.IS_ACTIVE', true); // Điều kiện IS_ACTIVE cho bảng permission
                })
                ->join('title as t', function ($join) {
                    $join->on('r.id', '=', 't.ROLE_ID')
                         ->where('t.IS_ACTIVE', true);  // Điều kiện IS_ACTIVE cho bảng title
                })
                ->join('user as u', function ($join) {
                    $join->on('u.id', '=', 't.USER_ID')
                         ->where('u.IS_ACTIVE', true);  // Điều kiện IS_ACTIVE cho bảng users
                })
                ->where([
                    ['u.id', '=', $this->ID], // So sánh với id của instance User hiện tại
                    ['rp.IS_ACTIVE', true]
                ]) 
                ->select('rp.PERMISSION_CODE AS CODE', 'p.NAME')
                ->get();
    }    

    public function profile() {
        return $this->hasOne(UserProfile::class, 'USER_ID', 'ID')
            // Where thêm điều kiện bảng
            ->where([
                ['user_profile.IS_DEFAULT', true],
                ['user_profile.STATUS', '=', AppConstant::STATUS_USING]
            ]);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(UserSocialAccount::class, 'USER_ID', 'ID');
    }

    /**
     * Staff = role ADMIN hoặc CHUYEN_VIEN đang active. Role USER (khách) không được vào admin.
     */
    public function hasStaffAccess(): bool
    {
        return DB::table('title as t')
            ->join('role as r', 'r.ID', '=', 't.ROLE_ID')
            ->where('t.USER_ID', $this->ID)
            ->where('t.STATUS', AppConstant::STATUS_USING)
            ->where('t.IS_ACTIVE', true)
            ->where('r.STATUS', AppConstant::STATUS_USING)
            ->where('r.IS_ACTIVE', true)
            ->whereIn('r.CODE', ['ADMIN', 'CHUYEN_VIEN'])
            ->exists();
    }

    public function isAdmin(): bool
    {
        return DB::table('title as t')
            ->join('role as r', 'r.ID', '=', 't.ROLE_ID')
            ->where('t.USER_ID', $this->ID)
            ->where('t.STATUS', AppConstant::STATUS_USING)
            ->where('t.IS_ACTIVE', true)
            ->where('r.STATUS', AppConstant::STATUS_USING)
            ->where('r.IS_ACTIVE', true)
            ->where('r.CODE', 'ADMIN')
            ->exists();
    }
}
