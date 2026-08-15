<?php

namespace Database\Seeders;

use App\Enum\AppConstant;
use App\Enum\AuthConstant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrUser = [
            [
                'ID' => AuthConstant::USER_SUPER_ADMIN_ID,
                'EMAIL' => AuthConstant::USER_SUPER_ADMIN_EMAIL,
                'USERNAME' => AuthConstant::USER_SUPER_ADMIN_USERNAME,
                'PASSWORD' => bcrypt(AuthConstant::USER_SUPER_ADMIN_PASSWORD),
                'FULL_NAME' => AuthConstant::USER_SUPER_ADMIN_FULL_NAME,
                'COUNT_LOGIN_FAIL' => null,
                'ACTIVE_KEY' => null,
                'RESET_KEY' => null,
                'RESET_DATE' => null,
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

        foreach ($arrUser as $index => $user) {
            $exists = DB::table('user')->where([
                ['ID', '=', $user['ID']]
            ])->exists();

            if (!$exists) {
                DB::table('user')->insert($user);
            } else {
                DB::table('user')->where([
                    ['ID', '=', $user['ID']]
                ])->update($user);
            }
        }

        $arrUserProfile = [
            [
                'ID' => 1,
                'USER_ID' => 1,
                'ADDRESS' => 'Đồ Chơi Win Win, đường DT605, xã Hòa Tiến, thành phố Đà Nẵng',
                'MOBILE' => '0905135818',
                'IS_DEFAULT' => true,
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

        foreach ($arrUserProfile as $index => $userProfile) {
            $exists = DB::table('user_profile')->where([
                ['ID', '=', $userProfile['ID']]
            ])->exists();

            if (!$exists) {
                DB::table('user_profile')->insert($userProfile);
            } else {
                DB::table('user_profile')
                    ->where('ID', $userProfile['ID'])
                    ->update($userProfile);
            }
        }
    }
}
