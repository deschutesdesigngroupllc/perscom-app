<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Jobs\RemoveInactiveAccounts;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RemoveInactiveAccountsTest extends TestCase
{
    public function test_command_will_queue_job(): void
    {
        Queue::fake();

        $this->artisan('perscom:inactive-accounts')->assertSuccessful();

        Queue::assertPushed(RemoveInactiveAccounts::class);
    }
}
