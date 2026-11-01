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

    public function test_background_tick_runs_sapo_fetch_only_while_the_switch_is_on(): void
    {
        SapoAutoFetch::setEnabled(false);
        $this->assertNotContains('fetch-sapo', BackgroundTasks::enabledNames());

        SapoAutoFetch::setEnabled(true);
        $this->assertContains('fetch-sapo', BackgroundTasks::enabledNames());
    }
}
