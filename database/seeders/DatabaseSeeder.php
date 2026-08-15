<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Deploy chỉ cần đăng nhập admin:
     *   php artisan db:seed --class=DeployAuthSeeder
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategoryPSeeder::class,
            ToyStoreBrandSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            TitleSeeder::class,
            DonHangPermissionSeeder::class,
        ]);
    }
}
