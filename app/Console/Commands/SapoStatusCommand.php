<?php

namespace App\Console\Commands;

use App\Service\SapoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SapoStatusCommand extends Command
{
    protected $signature = 'sapo:status {--ping : Gọi thử API products/count}';

    protected $description = 'Kiểm tra cấu hình Sapo (.env) và kết nối API';

    public function handle(SapoService $sapoService): int
    {
        $status = $sapoService->configurationStatus();

        $rows = [
            ['enabled', $status['enabled'] ? 'yes' : 'no'],
            ['store', $status['store'] !== '' ? $status['store'] : '(empty)'],
            ['product_type', $status['product_type'] !== '' ? $status['product_type'] : '(empty)'],
            ['missing', $status['missing'] === [] ? '-' : implode(', ', $status['missing'])],
            ['config_cached', file_exists(base_path('bootstrap/cache/config.php')) ? 'yes' : 'no'],
        ];

        foreach ($this->databaseStats() as $label => $value) {
            $rows[] = [$label, $value];
        }

        $this->table(['Key', 'Value'], $rows);

        if (! $status['enabled']) {
            $this->error('Sapo chưa sẵn sàng. Thêm biến thiếu vào .env rồi chạy: php artisan optimize:clear');

            return self::FAILURE;
        }

        if (! $this->option('ping')) {
            $this->info('Cấu hình OK. Thêm --ping để gọi thử API.');

            return self::SUCCESS;
        }

        try {
            $count = $sapoService->getProductsCount([
                'product_type' => $status['product_type'] !== '' ? $status['product_type'] : null,
            ]);
            $this->info('Kết nối Sapo OK. products/count = '.$count);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Gọi Sapo thất bại: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @return array<string, string>
     */
    private function databaseStats(): array
    {
        try {
            $cacheCount = Schema::hasTable('sapo_product_cache')
                ? DB::table('sapo_product_cache')->count()
                : null;
            $lastFetch = Schema::hasTable('sapo_sync_state')
                ? DB::table('sapo_sync_state')->where('SCOPE', 'products')->value('LAST_FETCH_API_SAPO')
                : null;
            $localCount = Schema::hasTable('product')
                ? DB::table('product')->whereNotNull('SAPO_ID')->count()
                : null;
        } catch (Throwable $e) {
            return ['db' => 'lỗi: '.$e->getMessage()];
        }

        return [
            'sapo_product_cache' => $cacheCount === null ? '(chưa migrate)' : (string) $cacheCount,
            'product có SAPO_ID' => $localCount === null ? '(chưa migrate)' : (string) $localCount,
            'last_fetch_api_sapo (UTC)' => $lastFetch ? (string) $lastFetch : '(chưa fetch lần nào)',
        ];
    }
}
