<?php

namespace App\Console\Commands;

use App\Support\OrderPhoneNotifier;
use Illuminate\Console\Command;

class DeliverPhonePushCommand extends Command
{
    protected $signature = 'phone-push:deliver {payload}';

    protected $description = 'Gửi một thông báo Web Push đã mã hóa';

    public function handle(OrderPhoneNotifier $notifier): int
    {
        $json = base64_decode((string) $this->argument('payload'), true);
        $payload = is_string($json) ? json_decode($json, true) : null;
        if (! is_array($payload)) {
            $this->line('SENT:0');

            return self::FAILURE;
        }

        $sent = $notifier->deliver([
            'title' => (string) ($payload['title'] ?? 'Đồ Chơi Win Win'),
            'body' => (string) ($payload['body'] ?? ''),
            'url' => (string) ($payload['url'] ?? url('/admin/don-hang/danh-sach')),
            'tag' => (string) ($payload['tag'] ?? 'ww-order'),
        ]);
        $this->line('SENT:'.$sent);

        return self::SUCCESS;
    }
}
