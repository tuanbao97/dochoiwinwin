<?php

namespace App\Jobs;

use App\Support\SapoOrderPuller;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Mỗi 30 giây kéo cập nhật Sapo theo mốc setting chung.
 */
class PullSapoOrderUpdatesJob
{
    use Dispatchable, SerializesModels;

    public function handle(SapoOrderPuller $puller): void
    {
        $puller->pull();
    }
}
