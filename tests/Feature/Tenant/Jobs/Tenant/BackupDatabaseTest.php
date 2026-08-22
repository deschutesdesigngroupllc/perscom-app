<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\BackupDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class BackupDatabaseTest extends TenantTestCase
{
    public function test_work_runs_the_backup_command_scoped_to_the_backups_disk(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', [
                '--only-to-disk' => 'backups',
                '--only-db' => true,
                '--timeout' => 1800,
            ])
            ->andReturn(0);

        new BackupDatabase($this->tenant->getKey())->work();
    }

    public function test_it_is_routed_to_the_backup_queue_when_tenancy_is_enabled(): void
    {
        Queue::fake();
        config(['tenancy.enabled' => true]);

        dispatch(new BackupDatabase($this->tenant->getKey()));

        Queue::assertPushed(
            BackupDatabase::class,
            fn (BackupDatabase $job): bool => $job->queue === 'backup'
                && $job->connection === 'central'
                && $job->tenantKey === $this->tenant->getKey()
        );
    }
}
