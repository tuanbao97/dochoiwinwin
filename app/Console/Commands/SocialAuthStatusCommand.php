<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class SocialAuthStatusCommand extends Command
{
    protected $signature = 'social:status';

    protected $description = 'Kiểm tra cấu hình đăng nhập Google/Facebook (.env) và callback URL';

    private const PROVIDERS = ['google' => 'Google', 'facebook' => 'Facebook'];

    public function handle(): int
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        $this->table(['Key', 'Value'], [
            ['APP_URL', $appUrl !== '' ? $appUrl : '(empty)'],
            ['config_cached', file_exists(base_path('bootstrap/cache/config.php')) ? 'yes' : 'no'],
            ['bảng user_social_account', $this->socialAccountTable()],
        ]);

        $rows = [];
        $missing = [];

        foreach (self::PROVIDERS as $provider => $label) {
            $clientId = (string) config('services.'.$provider.'.client_id');
            $clientSecret = (string) config('services.'.$provider.'.client_secret');
            $redirect = (string) config('services.'.$provider.'.redirect');

            if ($clientId === '' || $clientSecret === '') {
                $missing[] = $label;
            }

            $rows[] = [
                $label,
                $clientId !== '' ? $this->mask($clientId) : '(empty)',
                $clientSecret !== '' ? 'đã đặt' : '(empty)',
                $redirect !== '' ? $redirect : '(empty)',
            ];
        }

        $this->table(['Provider', 'client_id', 'client_secret', 'redirect (khai báo y hệt bên console)'], $rows);

        if ($missing !== []) {
            $this->error(
                'Thiếu cấu hình cho: '.implode(', ', $missing)
                .'. Thêm biến vào .env (xem .env.example) rồi chạy: php artisan optimize:clear'
            );

            return self::FAILURE;
        }

        if ($appUrl === '') {
            $this->warn('APP_URL đang rỗng nên redirect URI có thể sai. Đặt APP_URL đúng domain production.');
        } elseif (! Str::startsWith($appUrl, 'https://')) {
            $this->warn('APP_URL không dùng https. Google chỉ chấp nhận redirect URI https (trừ localhost).');
        }

        $this->info('Cấu hình OK. Nếu vẫn lỗi, đối chiếu redirect ở trên với Authorized redirect URI trong Google Cloud Console.');

        return self::SUCCESS;
    }

    private function socialAccountTable(): string
    {
        try {
            return Schema::hasTable('user_social_account') ? 'có' : '(chưa migrate)';
        } catch (Throwable $e) {
            return 'lỗi: '.$e->getMessage();
        }
    }

    private function mask(string $value): string
    {
        return Str::length($value) <= 12
            ? Str::mask($value, '*', 3)
            : Str::mask($value, '*', 6, Str::length($value) - 12);
    }
}
