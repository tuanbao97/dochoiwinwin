<?php

namespace App\Support;

use App\Models\AdminPhonePush;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class OrderPhoneNotifier
{
    /**
     * @return array{title: string, body: string, url: string, tag: string}
     */
    public static function payloadFor(Transaction $transaction): array
    {
        $code = $transaction->SAPO_ORDER_NAME ?: '#'.$transaction->ID;
        $name = trim((string) $transaction->USER_BUY_FULLNAME);
        $qty = (int) $transaction->TOTAL_QUANTITY;
        $total = number_format((int) $transaction->TOTAL_PRICE, 0, ',', '.').' ₫';
        $parts = array_values(array_filter([
            $name !== '' ? $name : null,
            $qty > 0 ? $qty.' sản phẩm' : null,
            $total,
        ]));

        return [
            'title' => 'Đơn mới '.$code,
            'body' => implode(' · ', $parts),
            'url' => url('/admin/don-hang/chi-tiet/dh-'.$transaction->ID),
            'tag' => 'ww-order-'.$transaction->ID,
        ];
    }

    public function notifyNewOrder(Transaction $transaction): void
    {
        try {
            $this->send(self::payloadFor($transaction));
        } catch (Throwable $e) {
            Log::warning('Không gửi được thông báo đơn mới tới điện thoại', [
                'transaction_id' => $transaction->ID,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function sendTest(): int
    {
        return $this->send([
            'title' => 'Đồ Chơi Win Win',
            'body' => 'Điện thoại đã nhận thông báo đơn hàng.',
            'url' => url('/admin/don-hang/danh-sach'),
            'tag' => 'ww-push-test',
        ]);
    }

    /**
     * @param  array{title: string, body: string, url: string, tag: string}  $payload
     */
    public function send(array $payload): int
    {
        if (PHP_OS_FAMILY === 'Windows' && PHP_SAPI !== 'cli') {
            return $this->sendViaCli($payload);
        }

        return $this->deliver($payload);
    }

    /**
     * Apache trên XAMPP không đưa OPENSSL_CONF tới thư viện OpenSSL.
     * Bản CLI có biến này ký được thông báo.
     *
     * @param  array{title: string, body: string, url: string, tag: string}  $payload
     */
    private function sendViaCli(array $payload): int
    {
        $encoded = base64_encode(json_encode($payload, JSON_UNESCAPED_UNICODE));
        $command = escapeshellarg($this->phpCli()).' '.escapeshellarg(base_path('artisan')).' phone-push:deliver '.escapeshellarg($encoded);
        $environment = [];
        foreach (array_merge($_SERVER, $_ENV) as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $environment[$key] = $value;
            }
        }
        $opensslConf = 'C:\\xampp\\php\\extras\\ssl\\openssl.cnf';
        if (is_file($opensslConf)) {
            $environment['OPENSSL_CONF'] = $opensslConf;
        }
        $environment['SystemRoot'] = $environment['SystemRoot'] ?? (getenv('SystemRoot') ?: 'C:\\Windows');

        $pipes = [];
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, base_path(), $environment);
        if (! is_resource($process)) {
            return 0;
        }
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        if (preg_match('/SENT:(\d+)/', (string) $output, $match) === 1) {
            return (int) $match[1];
        }

        return 0;
    }

    private function phpCli(): string
    {
        $binary = PHP_BINARY;
        if (preg_match('/php(?:\.exe)?$/i', $binary) === 1 && is_file($binary)) {
            return $binary;
        }
        $xampp = 'C:\\xampp\\php\\php.exe';
        if (is_file($xampp)) {
            return $xampp;
        }

        return $binary;
    }

    /**
     * @param  array{title: string, body: string, url: string, tag: string}  $payload
     */
    public function deliver(array $payload): int
    {
        $publicKey = (string) config('services.webpush.public_key');
        $privateKey = (string) config('services.webpush.private_key');
        if ($publicKey === '' || $privateKey === '') {
            return 0;
        }

        $subscriptions = AdminPhonePush::query()->get();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => (string) config('services.webpush.subject'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);
        $webPush->setReuseVAPIDHeaders(true);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->ENDPOINT,
                    'publicKey' => $subscription->PUBLIC_KEY,
                    'authToken' => $subscription->AUTH_TOKEN,
                    'contentEncoding' => $subscription->CONTENT_ENCODING ?: 'aes128gcm',
                ]),
                json_encode($payload, JSON_UNESCAPED_UNICODE)
            );
        }

        $sent = 0;
        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
                continue;
            }
            if ($report->isSubscriptionExpired()) {
                AdminPhonePush::query()
                    ->where('ENDPOINT_HASH', hash('sha256', $report->getEndpoint()))
                    ->delete();
            }
            Log::info('Thông báo điện thoại không tới được một máy', [
                'endpoint' => $report->getEndpoint(),
                'reason' => $report->getReason(),
            ]);
        }

        return $sent;
    }
}
