<?php

namespace App\Console\Commands;

use App\Service\SapoService;
use Illuminate\Console\Command;
use Throwable;

class SapoStatusCommand extends Command
{
    protected $signature = 'sapo:status {--ping : Gọi thử API products/count}';

    protected $description = 'Kiểm tra cấu hình Sapo (.env) và kết nối API';

    public function handle(SapoService $sapoService): int
    {
        $status = $sapoService->configurationStatus();

        $this->table(['Key', 'Value'], [
            ['enabled', $status['enabled'] ? 'yes' : 'no'],
            ['store', $status['store'] !== '' ? $status['store'] : '(empty)'],
            ['product_type', $status['product_type'] !== '' ? $status['product_type'] : '(empty)'],
            ['missing', $status['missing'] === [] ? '-' : implode(', ', $status['missing'])],
            ['config_cached', file_exists(base_path('bootstrap/cache/config.php')) ? 'yes' : 'no'],
        ]);

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
}
