<?php

namespace App\Console\Commands;

use App\Support\BackgroundTasks;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SuperviseBackgroundTasksCommand extends Command
{
    protected $signature = 'tasks:supervise';

    protected $description = 'Chạy mãi. Mỗi 60 giây xem từng công tắc và chạy việc đang bật.';

    public function handle(): int
    {
        $this->info('Job nền đang chạy. Mỗi '.BackgroundTasks::INTERVAL_SECONDS.' giây kiểm tra công tắc.');
        register_shutdown_function(static function (): void {
            BackgroundTasks::forgetBeat();
        });

        while (true) {
            BackgroundTasks::beat();

            try {
                $results = BackgroundTasks::tick();
                $enabled = BackgroundTasks::enabledNames();
                if ($enabled === []) {
                    $this->line('['.date('H:i:s').'] Không có công tắc nào bật.');
                } else {
                    foreach ($enabled as $name) {
                        $detail = (string) ($results[$name]['detail'] ?? 'Đã chạy.');
                        $this->line('['.date('H:i:s').'] '.$name.': '.$detail);
                    }
                }
            } catch (Throwable $e) {
                Log::warning('Job nền lỗi', ['message' => $e->getMessage()]);
                $this->error('['.date('H:i:s').'] '.$e->getMessage());
            }

            for ($second = 0; $second < BackgroundTasks::INTERVAL_SECONDS; $second++) {
                if ($second % 10 === 0) {
                    BackgroundTasks::beat();
                }
                sleep(1);
            }
        }
    }
}
