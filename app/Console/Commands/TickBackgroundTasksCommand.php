<?php

namespace App\Console\Commands;

use App\Support\BackgroundTasks;
use Illuminate\Console\Command;
use Throwable;

class TickBackgroundTasksCommand extends Command
{
    protected $signature = 'tasks:tick';

    protected $description = 'Chạy một vòng job nền (dùng cho cron / schedule:run, không cần giữ terminal).';

    public function handle(): int
    {
        BackgroundTasks::beat();

        try {
            $results = BackgroundTasks::tick();
            $enabled = BackgroundTasks::enabledNames();
            if ($enabled === []) {
                $this->line('Không có công tắc nào bật.');

                return self::SUCCESS;
            }

            foreach ($enabled as $name) {
                $detail = (string) ($results[$name]['detail'] ?? 'Đã chạy.');
                $this->line($name.': '.$detail);
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
