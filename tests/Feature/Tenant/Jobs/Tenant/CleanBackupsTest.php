<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\CleanBackups;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class CleanBackupsTest extends TenantTestCase
{
    public function test_work_runs_the_backup_clean_command(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:clean')
            ->andReturn(0);

        (new CleanBackups($this->tenant->getKey()))->work();
    }

    public function test_it_is_routed_to_the_clean_queue_when_tenancy_is_enabled(): void
    {
        Queue::fake();
        config(['tenancy.enabled' => true]);

        CleanBackups::dispatch($this->tenant->getKey());

        Queue::assertPushed(
            CleanBackups::class,
            fn (CleanBackups $job): bool => $job->queue === 'clean'
                && $job->connection === 'central'
                && $job->tenantKey === $this->tenant->getKey()
        );
    }
}
