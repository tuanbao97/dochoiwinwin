<?php

namespace App\Console\Commands;

use App\Jobs\SyncSapoCatalogJob;
use Illuminate\Console\Command;

class SapoSyncProductsCommand extends Command
{
    protected $signature = 'sapo:sync-products
        {--full : Bỏ qua last_fetch_api_sapo, GET all lại}
        {--now : Bỏ qua min interval}
        {--import-only : Chỉ import cache hiện có vào product local, không gọi API}';

    protected $description = 'Đồng bộ cache Sapo rồi import vào bảng product local (kèm tải ảnh)';

    public function handle(): int
    {
        @set_time_limit(0);
        $result = SyncSapoCatalogJob::dispatchSync(
            (bool) $this->option('full'),
            (bool) $this->option('now'),
            (bool) $this->option('import-only'),
        );
        $this->info(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }
}
