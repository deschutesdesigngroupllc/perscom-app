<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Jobs\Tenant\ProcessExpiringRecords;
use App\Jobs\Tenant\RunDailyUserReview;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class RunScheduledAutomationsCommandTest extends TestCase
{
    public function test_command_dispatches_tenant_batches(): void
    {
        Bus::fake();

        $this->artisan('perscom:scheduled-automations')->assertSuccessful();

        Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->name === 'Process Expiring Records');
        Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->name === 'Run Daily User Review');
    }

    public function test_command_dispatches_jobs_when_self_hosted(): void
    {
        config(['tenancy.enabled' => false]);

        Bus::fake();

        $this->artisan('perscom:scheduled-automations')->assertSuccessful();

        Bus::assertDispatched(ProcessExpiringRecords::class);
        Bus::assertDispatched(RunDailyUserReview::class);
    }
}
