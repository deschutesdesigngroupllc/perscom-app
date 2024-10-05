<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\BackupTenantDatabase;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class BackupDatabases extends Command
{
    protected $signature = 'perscom:backup';

    protected $description = 'Backs up a tenant\'s database.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant $tenant) => new BackupTenantDatabase($tenant->getKey()))
        )->name(
            name: 'Database Backups'
        )->onQueue(
            queue: 'backup'
        )->dispatch();

        $this->info('The database backup job has been dispatched.');

        return static::SUCCESS;
    }
}
