<?php

namespace App\Support;

use Laravel\Passport\Passport;
use phpseclib3\Crypt\RSA;
use RuntimeException;

class PassportKeys
{
    public static function publicPath(): string
    {
        return self::keyPath('oauth-public.key');
    }

    public static function privatePath(): string
    {
        return self::keyPath('oauth-private.key');
    }

    public static function exist(): bool
    {
        return is_file(self::publicPath()) && is_file(self::privatePath());
    }

    /**
     * Tạo cặp key OAuth nếu chưa có. Không phụ thuộc lệnh passport:keys của package.
     */
    public static function ensure(bool $force = false, int $length = 4096): void
    {
        if (! $force && self::exist()) {
            return;
        }

        [$publicKey, $privateKey] = self::createKeyPair($length);

        self::write(self::publicPath(), $publicKey, 0660);
        self::write(self::privatePath(), $privateKey, 0600);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function createKeyPair(int $length): array
    {
        if (class_exists(RSA::class)) {
            $key = RSA::createKey($length);

            return [(string) $key->getPublicKey(), (string) $key];
        }

        if (! function_exists('openssl_pkey_new')) {
            throw new RuntimeException(
                'Không tạo được OAuth key: thiếu phpseclib và OpenSSL. Cài laravel/passport hoặc bật extension openssl.'
            );
        }

        $resource = openssl_pkey_new([
            'private_key_bits' => $length,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        if ($resource === false) {
            throw new RuntimeException('openssl_pkey_new thất bại: '.openssl_error_string());
        }

        $privateKey = '';
        if (! openssl_pkey_export($resource, $privateKey)) {
            throw new RuntimeException('openssl_pkey_export thất bại: '.openssl_error_string());
        }

        $details = openssl_pkey_get_details($resource);
        $publicKey = is_array($details) ? (string) ($details['key'] ?? '') : '';
        if ($publicKey === '' || $privateKey === '') {
            throw new RuntimeException('Không xuất được cặp RSA key.');
        }

        return [$publicKey, $privateKey];
    }

    private static function keyPath(string $file): string
    {
        if (class_exists(Passport::class)) {
            return Passport::keyPath($file);
        }

        return storage_path($file);
    }

    private static function write(string $path, string $contents, int $mode): void
    {
        $dir = dirname($path);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new RuntimeException('Không tạo được thư mục: '.$dir);
        }

        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException('Không ghi được file: '.$path);
        }

        if (! windows_os()) {
            @chmod($path, $mode);
        }
    }
}
