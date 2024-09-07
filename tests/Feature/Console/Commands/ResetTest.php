<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Jobs\ResetDemoAccount;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ResetTest extends TestCase
{
    public function test_command_will_queue_reset_demo_job()
    {
        Queue::fake();

        $this->artisan('perscom:reset', [
            '--env' => 'demo',
        ])->assertSuccessful();

        Queue::assertPushed(ResetDemoAccount::class);
    }
}
