<?php

namespace App\Console\Commands;

use Database\Seeders\DeploySeeder;
use Database\Seeders\WinWinNewsSampleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DeploySeedCommand extends Command
{
    protected $signature = 'deploy:seed
        {--news : Seed lại tin tức mẫu (xóa tin tức hiện có)}
        {--strict : Trả về lỗi khi không seed được thay vì bỏ qua}';

    protected $description = 'Seed dữ liệu nền khi deploy: tài khoản, phân quyền, cấu hình, danh mục, tin tức';

    public function handle(): int
    {
        @set_time_limit(0);

        try {
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            return $this->skip('Không kết nối được database: ' . $e->getMessage());
        }

        if (! Schema::hasTable('user') || ! Schema::hasTable('category_p')) {
            return $this->skip('Database chưa chạy migration, bỏ qua seed.');
        }

        try {
            Artisan::call('db:seed', [
                '--class' => DeploySeeder::class,
                '--force' => true,
            ], $this->output);

            if ($this->option('news')) {
                Artisan::call('db:seed', [
                    '--class' => WinWinNewsSampleSeeder::class,
                    '--force' => true,
                ], $this->output);
            }
        } catch (Throwable $e) {
            return $this->skip('Seed thất bại: ' . $e->getMessage());
        }

        $this->info('Đã seed xong dữ liệu nền.');

        return self::SUCCESS;
    }

    private function skip(string $message): int
    {
        if ($this->option('strict')) {
            $this->error($message);

            return self::FAILURE;
        }

        $this->warn($message);

        return self::SUCCESS;
    }
}
