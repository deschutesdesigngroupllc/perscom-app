<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Central\BackupDatabase;
use App\Jobs\Tenant\BackupDatabase as BackupTenantDatabase;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Throwable;

class BackupDatabasesCommand extends Command implements Isolatable
{
    protected $signature = 'perscom:backup';

    protected $description = 'Backs up a all application databases.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(BackupTenantDatabase::class, name: 'Backup Tenant Databases', queue: 'backup');

        // The shared central database only exists in the multi-tenant SaaS deployment.
        if (config('tenancy.enabled')) {
            dispatch(new BackupDatabase);
        }

        $this->components->info('The database backup jobs have been dispatched.');

        return static::SUCCESS;
    }
}
