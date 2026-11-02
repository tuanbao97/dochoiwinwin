<?php

namespace App\Console\Commands;

use App\Support\BackgroundTasks;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SuperviseBackgroundTasksCommand extends Command
{
    protected $signature = 'tasks:supervise';

    protected $description = 'Tiến trình nền luôn chạy, mỗi 60 giây kiểm tra công tắc và chạy việc đang bật.';

    public function handle(): int
    {
        while (true) {
            try {
                BackgroundTasks::beat();
                BackgroundTasks::tick();
            } catch (Throwable $e) {
                Log::warning('Tiến trình nền lỗi', ['message' => $e->getMessage()]);
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
