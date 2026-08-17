<?php

use App\Jobs\SyncSapoCatalogJob;
use App\Jobs\RetryPendingSapoOrdersJob;
use App\Jobs\PullSapoOrderUpdatesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new SyncSapoCatalogJob)
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->name('sapo-catalog-sync');

Schedule::job(new RetryPendingSapoOrdersJob)
    ->everyMinute()
    ->withoutOverlapping(5)
    ->name('sapo-order-retry');

Schedule::job(new PullSapoOrderUpdatesJob)
    ->everyThirtySeconds()
    ->withoutOverlapping(5)
    ->name('sapo-order-pull-updates');
