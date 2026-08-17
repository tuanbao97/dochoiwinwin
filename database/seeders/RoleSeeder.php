<?php

namespace Database\Seeders;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrRole = [
            [
                'ID' => 1,
                'CODE' => 'ADMIN',
                'NAME' => 'Admin',
                'DESCRIPTION' => null,
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true
            ],
            [
                'ID' => 2,
                'CODE' => 'CHUYEN_VIEN',
                'NAME' => 'Chuyên viên',
                'DESCRIPTION' => null,
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true
            ],
            [
                'ID' => 3,
                'CODE' => 'USER',
                'NAME' => 'Người dùng',
                'DESCRIPTION' => 'Tài khoản khách hàng trên giao diện cửa hàng, không có quyền quản trị',
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'CRT_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'UPD_ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'CRT_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'UPD_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'STATUS' => AppConstant::STATUS_USING,
                'IS_ACTIVE' => true
            ]
            
        ];

        foreach ($arrRole as $index => $role) {
            $exists = DB::table('role')->where([
                ['ID', '=', $role['ID']]
            ])->exists();

            if (!$exists) {
                DB::table('role')->insert($role);
            } else {
                DB::table('role')->where([
                    ['ID', '=', $role['ID']]
                ])->update($role);
            }
        }
    }
}
