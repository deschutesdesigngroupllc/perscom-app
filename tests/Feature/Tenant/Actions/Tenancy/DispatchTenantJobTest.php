<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Tenancy;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\System\RemoveInactiveAccounts;
use App\Jobs\Tenant\CalculateSchedules;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Tests\Feature\Tenant\TenantTestCase;

class DispatchTenantJobTest extends TenantTestCase
{
    public function test_fans_out_per_tenant_when_tenancy_is_enabled(): void
    {
        Bus::fake();
        config(['tenancy.enabled' => true]);

        DispatchTenantJob::for(CalculateSchedules::class, name: 'Calculate Schedules');

        Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->jobs->isNotEmpty());
    }

    public function test_runs_once_against_central_when_self_hosted(): void
    {
        Bus::fake();
        config(['tenancy.enabled' => false]);

        DispatchTenantJob::for(CalculateSchedules::class, name: 'Calculate Schedules');

        Bus::assertDispatched(CalculateSchedules::class);
        Bus::assertNothingBatched();
    }

    public function test_skips_saas_only_jobs_when_self_hosted(): void
    {
        Bus::fake();
        config(['tenancy.enabled' => false]);

        DispatchTenantJob::for(RemoveInactiveAccounts::class, name: 'Remove Inactive Accounts');

        Bus::assertNothingDispatched();
        Bus::assertNothingBatched();
    }
}
