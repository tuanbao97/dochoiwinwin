<?php

namespace App\Support;

use App\Jobs\SyncSapoCatalogJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Danh sách việc cho lệnh `php artisan tasks:supervise`.
 * Lệnh đó tự chạy, mỗi 60 giây xem từng công tắc và chỉ gọi việc đang bật.
 */
class BackgroundTasks
{
    public const INTERVAL_SECONDS = 60;

    /**
     * Thêm công tắc mới vào đây: name, enabled, run.
     *
     * @return list<array{name: string, enabled: callable(): bool, run: callable(): mixed}>
     */
    public static function tasks(): array
    {
        return [
            [
                'name' => 'fetch-sapo',
                'enabled' => static fn (): bool => SapoAutoFetch::enabled(),
                'run' => static function (): array {
                    return SyncSapoCatalogJob::dispatchSync(false, false, false);
                },
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function enabledNames(): array
    {
        $names = [];
        foreach (self::tasks() as $task) {
            if (($task['enabled'])()) {
                $names[] = $task['name'];
            }
        }

        return $names;
    }

    /**
     * @return array<string, array{ok: bool, detail: string}>
     */
    public static function tick(): array
    {
        $previous = self::readStatus();
        $results = is_array($previous['results'] ?? null) ? $previous['results'] : [];
        $ran = false;

        foreach (self::tasks() as $task) {
            if (! ($task['enabled'])()) {
                continue;
            }

            $ran = true;
            try {
                $results[$task['name']] = self::summarize(($task['run'])());
            } catch (Throwable $e) {
                Log::warning('Job nền lỗi', [
                    'task' => $task['name'],
                    'message' => $e->getMessage(),
                ]);
                $results[$task['name']] = [
                    'ok' => false,
                    'detail' => $e->getMessage(),
                ];
            }
        }

        if ($ran) {
            self::writeStatus($results);
        }

        return $results;
    }

    public static function statusLine(bool $enabled): string
    {
        if (! $enabled) {
            return 'Đang tắt. Job bỏ qua việc này.';
        }

        $status = self::readStatus();
        $when = isset($status['at'])
            ? Carbon::createFromTimestamp((int) $status['at'])->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')
            : null;
        $detail = $status['results']['fetch-sapo']['detail'] ?? null;
        $last = ($when && $detail) ? ' Lần fetch gần nhất '.$when.': '.$detail.'.' : ' Chưa có lần fetch nào.';

        if (self::isRunning()) {
            return 'Job đang chạy, mỗi 60 giây kiểm tra công tắc.'.$last;
        }

        return 'Job chưa chạy. Chạy: php artisan tasks:supervise.'.$last;
    }

    public static function beat(): void
    {
        $path = self::heartbeatPath();
        $directory = dirname($path);
        if (! is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $payload = (string) getmypid();
        for ($try = 0; $try < 5; $try++) {
            if (@file_put_contents($path, $payload) !== false) {
                return;
            }
            usleep(100000);
        }
    }

    public static function forgetBeat(): void
    {
        $path = self::heartbeatPath();
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public static function isRunning(): bool
    {
        $path = self::heartbeatPath();
        if (! is_file($path)) {
            return false;
        }

        return (time() - (int) @filemtime($path)) <= 180;
    }

    /**
     * @param  array<string, array{ok: bool, detail: string}>  $results
     */
    private static function writeStatus(array $results): void
    {
        $path = self::statusPath();
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, json_encode([
            'at' => time(),
            'pid' => getmypid(),
            'results' => $results,
        ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return array<string, mixed>
     */
    private static function readStatus(): array
    {
        $raw = @file_get_contents(self::statusPath());
        $data = is_string($raw) ? json_decode($raw, true) : null;

        return is_array($data) ? $data : [];
    }

    /**
     * @return array{ok: bool, detail: string}
     */
    private static function summarize(mixed $value): array
    {
        if (! is_array($value)) {
            return ['ok' => true, 'detail' => 'Đã chạy.'];
        }

        $mode = (string) ($value['mode'] ?? '');
        $fetched = (int) ($value['fetched'] ?? 0);
        $detail = match ($mode) {
            'disabled' => 'Sapo chưa bật hoặc thiếu cấu hình trong .env.',
            'locked' => 'Đang có lượt fetch khác.',
            'fresh' => 'Chưa đủ 60 giây kể từ lần fetch trước.',
            'full' => 'Fetch full '.$fetched.' sản phẩm.',
            'incremental' => 'Fetch '.$fetched.' sản phẩm vừa đổi.',
            'import-only' => 'Import từ cache.',
            default => $mode !== '' ? $mode : 'Đã chạy.',
        };

        return [
            'ok' => ! in_array($mode, ['disabled', 'locked'], true),
            'detail' => $detail,
        ];
    }

    private static function heartbeatPath(): string
    {
        return storage_path('framework/background-tasks.heartbeat');
    }

    private static function statusPath(): string
    {
        return storage_path('framework/background-tasks.status');
    }
}
