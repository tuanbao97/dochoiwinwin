<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seed đầy đủ dữ liệu nền cho môi trường deploy:
 * tài khoản admin, phân quyền, cấu hình web, danh mục sản phẩm/tin tức, tin tức mẫu.
 *
 * Chạy nhiều lần được (upsert theo ID), không đụng tới dữ liệu sản phẩm đã sync từ Sapo.
 *
 *   php artisan db:seed --class=DeploySeeder --force
 *   php artisan deploy:seed
 *
 * Tin tức mẫu chỉ seed khi bảng news đang trống (seeder tin tức có truncate).
 */
class DeploySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            TitleSeeder::class,
            DonHangPermissionSeeder::class,
            OauthClientsSeeder::class,
            SettingSeeder::class,
            CategoryPSeeder::class,
            CategoryNSeeder::class,
        ]);

        if (Schema::hasTable('news') && DB::table('news')->count() === 0) {
            $this->call(WinWinNewsSampleSeeder::class);
        }

        if (function_exists('evictCacheDataFrontEnd')) {
            evictCacheDataFrontEnd();
        }
    }
}
