<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\System;

use App\Jobs\System\ResetDemoAccount;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Central\CentralTestCase;

class ResetDemoAccountTest extends CentralTestCase
{
    public function test_it_runs_the_install_command_in_demo_mode(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('perscom:install', [
                '-n' => true,
                '--demo' => true,
                '--force' => true,
            ])
            ->andReturn(0);

        (new ResetDemoAccount)->handle();

        $this->assertTrue(true);
    }

    public function test_it_uses_the_system_queue(): void
    {
        $this->assertSame('system', (new ResetDemoAccount)->queue);
    }

    public function test_it_declares_a_backoff_schedule(): void
    {
        $this->assertSame([1, 5, 10], (new ResetDemoAccount)->backoff());
    }
}
