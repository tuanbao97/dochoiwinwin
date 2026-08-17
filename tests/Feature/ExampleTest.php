<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // APP_URL local có sub-folder /laravel-do-choi-win-win/public;
        // dùng URL tuyệt đối để router test nhận đúng path "/".
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
    }
}
