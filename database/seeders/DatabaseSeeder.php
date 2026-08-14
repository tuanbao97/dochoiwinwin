<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategoryPSeeder::class,
            CategoryNSeeder::class,
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
