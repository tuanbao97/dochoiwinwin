<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Đồng bộ dữ liệu thương hiệu cũ sang Đồ Chơi Win Win.
 *
 * Chạy khi deploy hoặc nâng cấp dữ liệu:
 * php artisan db:seed --class=ToyStoreBrandSeeder
 */
class ToyStoreBrandSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            CategoryNSeeder::class,
            OauthClientsSeeder::class,
        ]);

        if (Schema::hasTable('user_profile') && Schema::hasColumn('user_profile', 'ADDRESS')) {
            DB::table('user_profile')
                ->where(function ($query) {
                    $query->where('ADDRESS', 'like', '%Trái Cây%')
                        ->orWhere('ADDRESS', 'like', '%trái cây%')
                        ->orWhere('ADDRESS', 'like', '%traicay%');
                })
                ->update([
                    'ADDRESS' => DB::raw(
                        "REPLACE(REPLACE(REPLACE(ADDRESS, 'Win Win Trái Cây Nhập Khẩu', 'Đồ Chơi Win Win'),"
                        . " 'Win Win Trái Cây', 'Đồ Chơi Win Win'), 'traicaywinwin.com', 'dochoiwinwin.com')"
                    ),
                    'UPD_DT' => now(),
                ]);
        }

        $this->removeLegacyFruitNews();

        if (Schema::hasTable('news') && DB::table('news')->count() === 0) {
            $this->call(WinWinNewsSampleSeeder::class);
        }

        if (function_exists('evictCacheDataFrontEnd')) {
            evictCacheDataFrontEnd();
        }
    }

    private function removeLegacyFruitNews(): void
    {
        if (! Schema::hasTable('news')) {
            return;
        }

        $columns = array_values(array_filter([
            'TITLE',
            'SUMMARY',
            'CONTENT_FORMAT',
            'CONTENT_RAW',
            'META_SEO_KEYWORDS',
            'META_SEO_DESCRIPTION',
        ], fn (string $column) => Schema::hasColumn('news', $column)));

        if ($columns === []) {
            return;
        }

        $legacyIds = DB::table('news')
            ->where(function ($query) use ($columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%trái cây%')
                        ->orWhere($column, 'like', '%traicay%')
                        ->orWhere($column, 'like', '%giỏ trái%');
                }
            })
            ->pluck('ID');

        if ($legacyIds->isEmpty()) {
            return;
        }

        if (Schema::hasTable('news_document_storage')) {
            DB::table('news_document_storage')->whereIn('NEWS_ID', $legacyIds)->delete();
        }
        if (Schema::hasTable('news_category')) {
            DB::table('news_category')->whereIn('NEWS_ID', $legacyIds)->delete();
        }
        DB::table('news')->whereIn('ID', $legacyIds)->delete();
    }
}
