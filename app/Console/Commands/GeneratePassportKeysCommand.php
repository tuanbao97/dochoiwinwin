<?php

namespace App\Console\Commands;

use App\Support\PassportKeys;
use Illuminate\Console\Command;
use Throwable;

class GeneratePassportKeysCommand extends Command
{
    protected $signature = 'passport:keys
        {--force : Ghi đè key nếu đã tồn tại}
        {--length=4096 : Độ dài private key}';

    protected $description = 'Tạo encryption keys cho API authentication (Passport)';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $length = max(2048, (int) $this->option('length'));

        if (! $force && PassportKeys::exist()) {
            $this->components->error('Encryption keys already exist. Use the --force option to overwrite them.');

            return self::FAILURE;
        }

        try {
            PassportKeys::ensure($force, $length);
        } catch (Throwable $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Encryption keys generated successfully.');

        return self::SUCCESS;
    }
}
