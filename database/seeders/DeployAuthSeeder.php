<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seed tối thiểu để đăng nhập admin khi deploy.
 *
 * Chạy trên server:
 *   php artisan db:seed --class=DeployAuthSeeder
 *
 * Tài khoản mặc định (AuthConstant):
 *   Email/Username: dochoiwinwin@gmail.com
 *   Password: Dochoiwinwin2026@?!
 *
 * Lưu ý Passport: nếu chưa có key thì chạy trước
 *   php artisan passport:keys
 */
class DeployAuthSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            TitleSeeder::class,
            SettingSeeder::class,
            OauthClientsSeeder::class,
            DonHangPermissionSeeder::class,
        ]);
    }
}
