<?php

namespace Tests\Feature\Console\Commands;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RunEnvoyerHeartbeatTest extends TestCase
{
    public function test_command_will_send_heartbeat()
    {
        Http::fake();

        $url = env('ENVOYER_HEARTBEAT_URL');

        $this->artisan('perscom:heartbeat')->assertSuccessful();

        Http::assertSent(function (Request $request) use ($url) {
            return $request->url() === $url;
        });
    }

    public function test_command_will_fail_when_heartbeat_returns_error()
    {
        $url = env('ENVOYER_HEARTBEAT_URL');

        Http::fake([
            $url => Http::response(status: 500),
        ]);

        $this->artisan('perscom:heartbeat')->assertFailed();
    }
}
