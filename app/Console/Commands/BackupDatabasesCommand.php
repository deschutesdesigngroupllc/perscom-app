<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\Central\CreateBackupTenantDatabasesBatch;
use App\Jobs\Central\BackupDatabase;
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
        CreateBackupTenantDatabasesBatch::handle();

        BackupDatabase::dispatch();

        $this->components->info('The database backup jobs have been dispatched.');

        return static::SUCCESS;
    }
}
