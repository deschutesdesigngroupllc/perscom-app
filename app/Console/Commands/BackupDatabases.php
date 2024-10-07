<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\BackupDatabase as BackupDatabaseAction;
use Illuminate\Console\Command;
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
        BackupDatabaseAction::handle();

        $this->info('The database backup job has been dispatched.');

        return static::SUCCESS;
    }
}
