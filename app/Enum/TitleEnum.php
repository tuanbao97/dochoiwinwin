<?php

namespace App\Enum;

enum TitleEnum : string
{
    case TITLE_ROLE_ADMIN = 'TITLE_ROLE_ADMIN';
    case TITLE_ROLE_CHUYEN_VIEN = 'TITLE_ROLE_CHUYEN_VIEN';
    case TITLE_ROLE_USER = 'TITLE_ROLE_USER';

     // Nếu bạn cần thêm thông tin, bạn có thể định nghĩa phương thức
    public function withRoleId() : int {
        return match ($this) {
            self::TITLE_ROLE_ADMIN => 1,
            self::TITLE_ROLE_CHUYEN_VIEN => 2,
            self::TITLE_ROLE_USER => 3,
        };
    }

    public function description() : string {
        return match ($this) {
            self::TITLE_ROLE_ADMIN => 'Role Admin',
            self::TITLE_ROLE_CHUYEN_VIEN => 'Role Chuyên Viên',
            self::TITLE_ROLE_USER => 'Role Người dùng',
        };
    }

    public static function getTitleEnumByRoleId(int $roleId) : TitleEnum {
        return match ($roleId) {
             1 => self::TITLE_ROLE_ADMIN,
             2 => self::TITLE_ROLE_CHUYEN_VIEN,
             3 => self::TITLE_ROLE_USER,
        };
    }
}
