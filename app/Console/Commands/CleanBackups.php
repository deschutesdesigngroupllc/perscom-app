<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\CleanBackups as CleanBackupsAction;
use Illuminate\Console\Command;
use Throwable;

class CleanBackups extends Command
{
    protected $signature = 'perscom:backup-clean';

    protected $description = 'Clean up the tenant backups.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        CleanBackupsAction::handle();

        $this->info('The database backup job has been dispatched.');

        return static::SUCCESS;
    }
}
