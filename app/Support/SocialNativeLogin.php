<?php

namespace App\Support;

/**
 * Trên điện thoại, đăng nhập Facebook/Zalo ưu tiên mở app.
 * Trình duyệt nhúng (web trong Facebook, Zalo, Instagram) được đẩy ra Chrome
 * để app nhận được yêu cầu đăng nhập.
 */
class SocialNativeLogin
{
    public function handoff(string $provider, string $userAgent, string $currentUrl, string $oauthUrl): ?array
    {
        if (! in_array($provider, ['facebook', 'zalo'], true)) {
            return null;
        }

        if ($this->isInAppBrowser($userAgent)) {
            $external = $this->externalBrowserUrl($currentUrl, $userAgent);
            if ($external === null) {
                return null;
            }

            return [
                'appUrl' => $external,
                'webUrl' => $oauthUrl,
            ];
        }

        if (! $this->isAndroid($userAgent)) {
            return null;
        }

        $package = $provider === 'zalo' ? 'com.zing.zalo' : 'com.facebook.katana';

        return [
            'appUrl' => $this->androidIntent($oauthUrl, $package),
            'webUrl' => $oauthUrl,
        ];
    }

    public function isInAppBrowser(string $userAgent): bool
    {
        return (bool) preg_match('/FBAN|FBAV|FB_IAB|FBIOS|Instagram|Zalo|TikTok|musical_ly|Line\//i', $userAgent);
    }

    public function androidIntent(string $httpsUrl, string $package): string
    {
        $parts = parse_url($httpsUrl);
        $host = $parts['host'] ?? '';
        $path = $parts['path'] ?? '';
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?'.$parts['query'] : '';

        return 'intent://'.$host.$path.$query
            .'#Intent;scheme=https;package='.$package
            .';S.browser_fallback_url='.rawurlencode($httpsUrl)
            .';end';
    }

    private function externalBrowserUrl(string $httpsUrl, string $userAgent): ?string
    {
        if ($this->isAndroid($userAgent)) {
            return $this->androidIntent($httpsUrl, 'com.android.chrome');
        }

        if (! $this->isIos($userAgent)) {
            return null;
        }

        $parts = parse_url($httpsUrl);
        $host = $parts['host'] ?? '';
        $path = $parts['path'] ?? '';
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?'.$parts['query'] : '';

        return 'googlechromes://'.$host.$path.$query;
    }

    private function isAndroid(string $userAgent): bool
    {
        return (bool) preg_match('/Android/i', $userAgent);
    }

    private function isIos(string $userAgent): bool
    {
        return (bool) preg_match('/iPhone|iPad|iPod/i', $userAgent);
    }
}
