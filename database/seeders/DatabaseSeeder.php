<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Deploy dùng bộ đầy đủ:
     *   php artisan deploy:seed
     * Chỉ cần tài khoản đăng nhập:
     *   php artisan db:seed --class=DeployAuthSeeder
     */
    public function run(): void
    {
        $this->call([
            DeploySeeder::class,
            ToyStoreBrandSeeder::class,
        ]);
    }
}
