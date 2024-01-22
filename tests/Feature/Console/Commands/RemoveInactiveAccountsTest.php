<?php

namespace Tests\Feature\Console\Commands;

use App\Jobs\RemoveInactiveAccounts;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RemoveInactiveAccountsTest extends TestCase
{
    public function test_command_will_queue_job()
    {
        Queue::fake();

        $this->artisan('perscom:inactive-accounts')->assertSuccessful();

        Queue::assertPushed(RemoveInactiveAccounts::class);
    }
}
