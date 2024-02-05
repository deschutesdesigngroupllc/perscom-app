<?php

namespace Tests\Feature\Console\Commands;

use App\Jobs\ResetDemoAccount;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ResetDemoAccountTest extends TestCase
{
    public function test_command_will_queue_job()
    {
        Queue::fake();

        $this->artisan('perscom:demo')->assertSuccessful();

        Queue::assertPushed(ResetDemoAccount::class);
    }
}
