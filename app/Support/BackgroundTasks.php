<?php

namespace App\Support;

use App\Jobs\SyncSapoCatalogJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Một tiến trình nền luôn chạy. Mỗi 60 giây kiểm tra từng công tắc
 * và chỉ gọi việc tương ứng khi công tắc đang bật.
 *
 * Trên production PHP-FPM, PHP_BINARY là php-fpm nên phải tìm đúng binary CLI.
 * Nếu server chặn exec, mỗi request web (sau khi đã trả trang) vẫn fetch một lần
 * khi đã quá 60 giây.
 */
class BackgroundTasks
{
    public const INTERVAL_SECONDS = 60;

    private const SPAWN_RETRY_SECONDS = 30;

    private static bool $webTickScheduled = false;

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

    public static function tick(): void
    {
        $results = [];
        foreach (self::tasks() as $task) {
            if (! ($task['enabled'])()) {
                continue;
            }

            try {
                $results[$task['name']] = self::summarize(($task['run'])());
            } catch (Throwable $e) {
                Log::warning('Tiến trình nền lỗi', [
                    'task' => $task['name'],
                    'message' => $e->getMessage(),
                ]);
                $results[$task['name']] = [
                    'ok' => false,
                    'detail' => $e->getMessage(),
                ];
            }
        }

        self::writeStatus($results);
    }

    public static function ensureRunning(): void
    {
        if (app()->runningUnitTests() || app()->runningInConsole()) {
            return;
        }

        try {
            if (self::isRunning()) {
                return;
            }

            $attempt = self::readSpawnAttempt();
            $age = is_array($attempt) ? time() - (int) ($attempt['at'] ?? 0) : null;

            if ($age === null || $age >= self::SPAWN_RETRY_SECONDS) {
                $error = self::spawn();
                if ($error !== null) {
                    self::scheduleWebTick();
                }

                return;
            }

            // Vừa gọi spawn mà heartbeat chưa có: đợi tiến trình lên.
            // Lỗi spawn hoặc sau vài giây vẫn chết thì fetch ngay trong request này.
            if (($attempt['error'] ?? '') !== '' || $age >= 5) {
                self::scheduleWebTick();
            }
        } catch (Throwable $e) {
            Log::warning('Không bật được tiến trình nền', [
                'message' => $e->getMessage(),
            ]);
            self::scheduleWebTick();
        }
    }

    public static function statusLine(bool $enabled): string
    {
        if (! $enabled) {
            return 'Đang tắt. Tiến trình nền bỏ qua việc này.';
        }

        $status = self::readStatus();
        $when = isset($status['at'])
            ? Carbon::createFromTimestamp((int) $status['at'])->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')
            : null;
        $detail = $status['results']['fetch-sapo']['detail'] ?? null;
        $last = ($when && $detail) ? 'Lần fetch gần nhất '.$when.': '.$detail.'.' : 'Chưa có lần fetch nào.';

        if (self::isRunning()) {
            return 'Đang chạy, mỗi 60 giây fetch một lần. '.$last;
        }

        $error = (string) (self::readSpawnAttempt()['error'] ?? '');

        return 'Đang bật lại. '.$last.($error !== '' ? ' '.$error : '');
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

        // Beat mỗi giây khi đang chờ. Fetch Sapo thì tạm dừng beat, nên vẫn
        // coi là sống trong vài phút nếu PID còn. File quá cũ là tiến trình đã chết.
        $age = time() - (int) @filemtime($path);
        if ($age > 180) {
            return false;
        }
        if ($age <= 15) {
            return true;
        }

        $pid = (int) @file_get_contents($path);
        if ($pid <= 0) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            if (! self::canCall('shell_exec')) {
                return true;
            }
            $output = shell_exec('tasklist /FI "PID eq '.$pid.'" /NH');

            return is_string($output) && preg_match('/\b'.$pid.'\b/', $output) === 1;
        }

        if (function_exists('posix_kill')) {
            return @posix_kill($pid, 0);
        }

        if (@is_dir('/proc/'.$pid)) {
            return true;
        }

        return true;
    }

    public static function isCliBinaryName(string $path): bool
    {
        $base = strtolower(basename(str_replace('\\', '/', $path)));

        return preg_match('/^php(\d+\.\d+)?(\.exe)?$/', $base) === 1;
    }

    private static function scheduleWebTick(): void
    {
        if (self::$webTickScheduled) {
            return;
        }
        self::$webTickScheduled = true;

        app()->terminating(function () {
            if (self::isRunning()) {
                return;
            }
            if (! self::claimTickSlot()) {
                return;
            }

            ignore_user_abort(true);
            @set_time_limit(0);
            self::tick();
        });
    }

    private static function claimTickSlot(): bool
    {
        $path = storage_path('framework/background-tasks.webtick');
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $handle = fopen($path, 'c');
        if ($handle === false || ! flock($handle, LOCK_EX | LOCK_NB)) {
            if (is_resource($handle)) {
                fclose($handle);
            }

            return false;
        }

        $last = (int) stream_get_contents($handle);
        if ($last > 0 && (time() - $last) < self::INTERVAL_SECONDS) {
            flock($handle, LOCK_UN);
            fclose($handle);

            return false;
        }

        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, (string) time());
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        return true;
    }

    /**
     * @return string|null Lỗi spawn, null nếu đã gọi được lệnh.
     */
    private static function spawn(): ?string
    {
        $php = self::phpCli();
        $error = null;
        if (! self::isCliBinaryName($php) || ! is_file($php)) {
            $error = 'Không tìm thấy PHP CLI (binary web là php-fpm, không chạy được artisan).';
            self::rememberSpawn($error);

            return $error;
        }

        $artisan = base_path('artisan');
        $log = storage_path('logs/background-tasks.log');
        $logDirectory = dirname($log);
        if (! is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        file_put_contents($log, '['.date('Y-m-d H:i:s').'] spawn '.$php.PHP_EOL, FILE_APPEND);

        if (PHP_OS_FAMILY === 'Windows') {
            if (! self::canCall('popen')) {
                $error = 'Server chặn popen nên không bật được tiến trình nền.';
                self::rememberSpawn($error);

                return $error;
            }
            $openssl = 'C:\\xampp\\php\\extras\\ssl\\openssl.cnf';
            $prefix = is_file($openssl) ? 'set OPENSSL_CONF='.self::quote($openssl).' && ' : '';
            $command = 'cmd /C '.$prefix.'start "" /B '.self::quote($php).' '.self::quote($artisan).' tasks:supervise >> '.self::quote($log).' 2>&1';
            $handle = popen($command, 'r');
            if (is_resource($handle)) {
                pclose($handle);
            }
            self::rememberSpawn(null);

            return null;
        }

        if (! self::canCall('exec')) {
            $error = 'Server chặn exec nên không giữ được tiến trình nền.';
            self::rememberSpawn($error);

            return $error;
        }

        $inner = escapeshellarg($php).' '.escapeshellarg($artisan).' tasks:supervise';
        $logArg = escapeshellarg($log);
        $detached = 'nohup '.$inner.' >> '.$logArg.' 2>&1 < /dev/null &';
        if (is_executable('/usr/bin/setsid') || is_executable('/bin/setsid')) {
            $detached = 'setsid '.$detached;
        }
        exec($detached, $output, $code);
        if ($code !== 0) {
            $error = 'Lệnh bật tiến trình nền trả mã '.$code.'.';
            self::rememberSpawn($error);

            return $error;
        }

        self::rememberSpawn(null);

        return null;
    }

    private static function phpCli(): string
    {
        $preferred = PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;
        $preferredShort = (string) PHP_MAJOR_VERSION.PHP_MINOR_VERSION;
        $same = [];
        $other = [];

        foreach (self::phpCliCandidates() as $path) {
            if (! self::isCliBinaryName($path) || ! is_file($path)) {
                continue;
            }
            $haystack = str_replace('\\', '/', $path);
            if (str_contains($haystack, $preferred) || str_contains($haystack, '/'.$preferredShort.'/') || str_contains($haystack, 'php'.$preferredShort)) {
                $same[] = $path;
            } else {
                $other[] = $path;
            }
        }

        if ($same !== []) {
            return $same[0];
        }
        if ($other !== []) {
            return $other[0];
        }

        return PHP_BINARY;
    }

    /**
     * @return list<string>
     */
    private static function phpCliCandidates(): array
    {
        $candidates = [];
        if (is_string(PHP_BINARY) && PHP_BINARY !== '') {
            $candidates[] = PHP_BINARY;
        }
        if (defined('PHP_BINDIR')) {
            $candidates[] = PHP_BINDIR.DIRECTORY_SEPARATOR.'php';
            $candidates[] = dirname(PHP_BINDIR).DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'php';
        }
        $candidates[] = 'C:\\xampp\\php\\php.exe';

        foreach (['8.4', '8.3', '8.2', '8.1', '8.0'] as $version) {
            $short = str_replace('.', '', $version);
            $candidates[] = '/usr/bin/php'.$version;
            $candidates[] = '/usr/local/bin/php'.$version;
            $candidates[] = '/opt/alt/php'.$short.'/usr/bin/php';
            $candidates[] = '/www/server/php/'.$short.'/bin/php';
        }
        $candidates[] = '/usr/bin/php';
        $candidates[] = '/usr/local/bin/php';

        foreach (['/www/server/php/*/bin/php', '/opt/alt/php*/usr/bin/php', '/usr/bin/php*', '/usr/local/bin/php*'] as $pattern) {
            $found = @glob($pattern);
            if (is_array($found)) {
                foreach ($found as $path) {
                    $candidates[] = $path;
                }
            }
        }

        if (self::canCall('shell_exec')) {
            $which = trim((string) shell_exec('command -v php 2>/dev/null'));
            if ($which !== '') {
                $candidates[] = $which;
            }
        }

        return $candidates;
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

    private static function rememberSpawn(?string $error): void
    {
        $path = self::spawnAttemptPath();
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, json_encode([
            'at' => time(),
            'error' => $error ?? '',
            'php' => PHP_BINARY,
        ], JSON_UNESCAPED_UNICODE));

        if ($error !== null) {
            Log::warning('Không bật được tiến trình nền', ['message' => $error]);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function readSpawnAttempt(): ?array
    {
        $raw = @file_get_contents(self::spawnAttemptPath());
        $data = is_string($raw) ? json_decode($raw, true) : null;

        return is_array($data) ? $data : null;
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

    private static function canCall(string $function): bool
    {
        if (! function_exists($function)) {
            return false;
        }

        $disabled = array_map('trim', explode(',', strtolower((string) ini_get('disable_functions'))));

        return ! in_array(strtolower($function), $disabled, true);
    }

    private static function heartbeatPath(): string
    {
        return storage_path('framework/background-tasks.heartbeat');
    }

    private static function statusPath(): string
    {
        return storage_path('framework/background-tasks.status');
    }

    private static function spawnAttemptPath(): string
    {
        return storage_path('framework/background-tasks.spawn');
    }

    private static function quote(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }
}
