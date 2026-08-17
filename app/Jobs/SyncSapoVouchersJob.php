<?php

namespace App\Jobs;

use App\Support\SapoVoucherSynchronizer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncSapoVouchersJob implements ShouldQueue
{
    use Queueable;

    public function handle(SapoVoucherSynchronizer $synchronizer): void
    {
        $synchronizer->sync();
    }
}
