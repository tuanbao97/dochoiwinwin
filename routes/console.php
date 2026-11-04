<?php

use App\Jobs\SyncSapoCatalogJob;
use App\Jobs\RetryPendingSapoOrdersJob;
use App\Jobs\PullSapoOrderUpdatesJob;
use App\Jobs\SyncSapoVouchersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Job nền (fetch Sapo khi công tắc bật…): chạy qua cron, không cần giữ terminal.
Schedule::command('tasks:tick')
    ->everyMinute()
    ->withoutOverlapping(2)
    ->name('background-tasks-tick');

Schedule::job(new SyncSapoCatalogJob)
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->name('sapo-catalog-sync');

Schedule::job(new SyncSapoVouchersJob)
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->name('sapo-voucher-sync');

Schedule::job(new RetryPendingSapoOrdersJob)
    ->everyMinute()
    ->withoutOverlapping(5)
    ->name('sapo-order-retry');

Schedule::job(new PullSapoOrderUpdatesJob)
    ->everyMinute()
    ->withoutOverlapping(5)
    ->name('sapo-order-pull-updates');
