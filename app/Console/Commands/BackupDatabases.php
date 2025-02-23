<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\BackupTenantDatabases;
use App\Jobs\Central\BackupCentralDatabase;
use Illuminate\Console\Command;
use Throwable;

class BackupDatabases extends Command
{
    protected $signature = 'perscom:backup';

    protected $description = 'Backs up a all application databases.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        BackupTenantDatabases::handle();

        BackupCentralDatabase::dispatch();

        $this->info('The database backup jobs have been dispatched.');

        return static::SUCCESS;
    }
}
