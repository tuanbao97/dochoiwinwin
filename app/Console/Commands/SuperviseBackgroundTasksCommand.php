<?php

namespace App\Console\Commands;

use App\Support\BackgroundTasks;
use Illuminate\Console\Command;

class SuperviseBackgroundTasksCommand extends Command
{
    protected $signature = 'tasks:supervise';

    protected $description = 'Tiến trình nền luôn chạy, mỗi 60 giây kiểm tra công tắc và chạy việc đang bật.';

    public function handle(): int
    {
        BackgroundTasks::beat();

        try {
            while (true) {
                BackgroundTasks::beat();
                BackgroundTasks::tick();

                for ($second = 0; $second < BackgroundTasks::INTERVAL_SECONDS; $second++) {
                    BackgroundTasks::beat();
                    sleep(1);
                }
            }
        } finally {
            BackgroundTasks::forgetBeat();
        }
    }
}
