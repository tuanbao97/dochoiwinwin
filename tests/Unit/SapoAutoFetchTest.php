<?php

namespace Tests\Unit;

use App\Support\BackgroundTasks;
use App\Support\SapoAutoFetch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SapoAutoFetchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_auto_fetch_switch_saves_on_and_off(): void
    {
        SapoAutoFetch::setEnabled(false);
        $this->assertFalse(SapoAutoFetch::enabled());

        SapoAutoFetch::setEnabled(true);
        $this->assertTrue(SapoAutoFetch::enabled());

        SapoAutoFetch::setEnabled(false);
        $this->assertFalse(SapoAutoFetch::enabled());
    }

    public function test_php_cli_name_rejects_fpm_binary(): void
    {
        $this->assertTrue(BackgroundTasks::isCliBinaryName('/usr/bin/php'));
        $this->assertTrue(BackgroundTasks::isCliBinaryName('/usr/bin/php8.2'));
        $this->assertTrue(BackgroundTasks::isCliBinaryName('C:\\xampp\\php\\php.exe'));
        $this->assertFalse(BackgroundTasks::isCliBinaryName('/usr/sbin/php-fpm8.2'));
        $this->assertFalse(BackgroundTasks::isCliBinaryName('/usr/bin/php-cgi'));
    }

    public function test_background_tick_runs_sapo_fetch_only_while_the_switch_is_on(): void
    {
        SapoAutoFetch::setEnabled(false);
        $this->assertNotContains('fetch-sapo', BackgroundTasks::enabledNames());

        SapoAutoFetch::setEnabled(true);
        $this->assertContains('fetch-sapo', BackgroundTasks::enabledNames());
    }
}
