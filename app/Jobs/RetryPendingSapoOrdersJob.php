<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Service\SapoService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RetryPendingSapoOrdersJob
{
    use Dispatchable, SerializesModels;

    public function handle(SapoService $sapo): void
    {
        if (! $sapo->isEnabled()) {
            return;
        }

        Transaction::query()
            ->whereNull('SAPO_ORDER_ID')
            ->whereIn('SAPO_SYNC_STATUS', ['PENDING', 'FAILED'])
            ->where('SAPO_SYNC_ATTEMPTS', '<', 5)
            ->orderBy('ID')
            ->limit(20)
            ->pluck('ID')
            ->each(static fn ($id) => SyncSapoOrderJob::dispatchSync((int) $id));
    }
}
