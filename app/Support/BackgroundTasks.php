<?php

namespace App\Support;

use App\Jobs\SyncSapoCatalogJob;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Một tiến trình nền luôn chạy. Mỗi 60 giây kiểm tra từng công tắc
 * và chỉ gọi việc tương ứng khi công tắc đang bật.
 */
class BackgroundTasks
{
    public const INTERVAL_SECONDS = 60;

    /**
     * Thêm công tắc mới vào đây: name, enabled, run.
     *
     * @return list<array{name: string, enabled: callable(): bool, run: callable(): void}>
     */
    public static function tasks(): array
    {
        return [
            [
                'name' => 'fetch-sapo',
                'enabled' => static fn (): bool => SapoAutoFetch::enabled(),
                'run' => static function (): void {
                    SyncSapoCatalogJob::dispatchSync(false, false, false);
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

    public static function tick(): void
    {
        foreach (self::tasks() as $task) {
            if (! ($task['enabled'])()) {
                continue;
            }

            try {
                ($task['run'])();
            } catch (Throwable $e) {
                Log::warning('Tiến trình nền lỗi', [
                    'task' => $task['name'],
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    public static function ensureRunning(): void
    {
        if (app()->runningUnitTests() || self::isRunning()) {
            return;
        }

        $lockPath = storage_path('framework/background-tasks.lock');
        $directory = dirname($lockPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $lock = fopen($lockPath, 'c');
        if ($lock === false || ! flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) {
                fclose($lock);
            }

            return;
        }

        try {
            if (self::isRunning()) {
                return;
            }

            self::spawn();
            $deadline = microtime(true) + 3;
            while (microtime(true) < $deadline && ! self::isRunning()) {
                usleep(100000);
            }
        } catch (Throwable $e) {
            Log::warning('Không bật được tiến trình nền', [
                'message' => $e->getMessage(),
            ]);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    public static function beat(): void
    {
        $path = self::heartbeatPath();
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, (string) getmypid());
    }

    public static function forgetBeat(): void
    {
        $path = self::heartbeatPath();
        if (is_file($path)) {
            unlink($path);
        }
    }

    public static function isRunning(): bool
    {
        $pid = (int) @file_get_contents(self::heartbeatPath());
        if ($pid <= 0) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('tasklist /FI "PID eq '.$pid.'" /NH');

            return is_string($output) && preg_match('/\b'.$pid.'\b/', $output) === 1;
        }

        if (function_exists('posix_kill')) {
            return posix_kill($pid, 0);
        }

        return is_dir('/proc/'.$pid);
    }

    private static function spawn(): void
    {
        $php = self::phpCli();
        $artisan = base_path('artisan');
        $log = storage_path('logs/background-tasks.log');
        $logDirectory = dirname($log);
        if (! is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $openssl = 'C:\\xampp\\php\\extras\\ssl\\openssl.cnf';
            $prefix = is_file($openssl) ? 'set OPENSSL_CONF='.self::quote($openssl).' && ' : '';
            $command = 'cmd /C '.$prefix.'start "" /B '.self::quote($php).' '.self::quote($artisan).' tasks:supervise >> '.self::quote($log).' 2>&1';
            $handle = popen($command, 'r');
            if (is_resource($handle)) {
                pclose($handle);
            }

            return;
        }

        $command = 'nohup '.escapeshellarg($php).' '.escapeshellarg($artisan).' tasks:supervise >> '.escapeshellarg($log).' 2>&1 &';
        exec($command);
    }

    private static function heartbeatPath(): string
    {
        return storage_path('framework/background-tasks.heartbeat');
    }

    private static function phpCli(): string
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

    private static function quote(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }
}
