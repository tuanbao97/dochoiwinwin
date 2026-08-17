<?php

namespace App\Console\Commands;

use App\Support\SapoVoucherSynchronizer;
use Illuminate\Console\Command;
use Throwable;

class SyncSapoVouchersCommand extends Command
{
    protected $signature = 'sapo:sync-vouchers';

    protected $description = 'Đồng bộ quy tắc và mã giảm giá từ Sapo về database local';

    public function handle(SapoVoucherSynchronizer $synchronizer): int
    {
        try {
            $result = $synchronizer->sync();
            $this->info(
                'Đã đồng bộ '.$result['rules'].' quy tắc và '.$result['codes'].' mã giảm giá từ Sapo.'
            );

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
