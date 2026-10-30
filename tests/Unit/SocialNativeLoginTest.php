<?php

namespace Tests\Unit;

use App\Support\SocialNativeLogin;
use Tests\TestCase;

class SocialNativeLoginTest extends TestCase
{
    private SocialNativeLogin $login;

    protected function setUp(): void
    {
        parent::setUp();
        $this->login = new SocialNativeLogin;
    }

    public function test_desktop_browser_keeps_the_web_login(): void
    {
        $handoff = $this->login->handoff(
            'zalo',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
            'https://dochoiwinwin.com/auth/zalo/redirect',
            'https://oauth.zaloapp.com/v4/permission?app_id=1'
        );

        $this->assertNull($handoff);
    }

    public function test_android_chrome_opens_the_zalo_app_and_falls_back_to_the_web(): void
    {
        $oauth = 'https://oauth.zaloapp.com/v4/permission?app_id=1&state=abc';
        $handoff = $this->login->handoff(
            'zalo',
            'Mozilla/5.0 (Linux; Android 14; Pixel 7) AppleWebKit/537.36 Chrome/120.0.0.0 Mobile Safari/537.36',
            'https://dochoiwinwin.com/auth/zalo/redirect',
            $oauth
        );

        $this->assertNotNull($handoff);
        $this->assertSame($oauth, $handoff['webUrl']);
        $this->assertStringStartsWith('intent://oauth.zaloapp.com/v4/permission?app_id=1&state=abc#Intent;scheme=https;package=com.zing.zalo;', $handoff['appUrl']);
        $this->assertStringContainsString('S.browser_fallback_url='.rawurlencode($oauth), $handoff['appUrl']);
    }

    public function test_android_chrome_opens_the_facebook_app(): void
    {
        $oauth = 'https://www.facebook.com/v23.0/dialog/oauth?client_id=9';
        $handoff = $this->login->handoff(
            'facebook',
            'Mozilla/5.0 (Linux; Android 14; Pixel 7) AppleWebKit/537.36 Chrome/120.0.0.0 Mobile Safari/537.36',
            'https://dochoiwinwin.com/auth/facebook/redirect',
            $oauth
        );

        $this->assertNotNull($handoff);
        $this->assertStringContainsString('package=com.facebook.katana', $handoff['appUrl']);
    }

    public function test_in_app_browser_leaves_the_inner_web_for_chrome(): void
    {
        $current = 'https://dochoiwinwin.com/auth/zalo/redirect?redirect=https%3A%2F%2Fdochoiwinwin.com%2F';
        $handoff = $this->login->handoff(
            'zalo',
            'Mozilla/5.0 (Linux; Android 14; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/120.0.0.0 Mobile Safari/537.36 Zalo',
            $current,
            'https://oauth.zaloapp.com/v4/permission?app_id=1'
        );

        $this->assertNotNull($handoff);
        $this->assertStringStartsWith('intent://dochoiwinwin.com/auth/zalo/redirect', $handoff['appUrl']);
        $this->assertStringContainsString('package=com.android.chrome', $handoff['appUrl']);
        $this->assertStringContainsString('S.browser_fallback_url='.rawurlencode($current), $handoff['appUrl']);
    }

    public function test_iphone_safari_keeps_the_provider_page(): void
    {
        $handoff = $this->login->handoff(
            'facebook',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Version/17.0 Mobile/15E148 Safari/604.1',
            'https://dochoiwinwin.com/auth/facebook/redirect',
            'https://www.facebook.com/v23.0/dialog/oauth?client_id=9'
        );

        $this->assertNull($handoff);
    }
}
