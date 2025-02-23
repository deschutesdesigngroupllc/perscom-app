<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\CleanBackups as CleanBackupsAction;
use App\Jobs\Central\CleanCentralBackups;
use Illuminate\Console\Command;
use Throwable;

class CleanBackups extends Command
{
    protected $signature = 'perscom:backup-clean';

    protected $description = 'Clean up the all application backups.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        CleanBackupsAction::handle();

        CleanCentralBackups::dispatch();

        $this->info('The database cleanup jobs have been dispatched.');

        return static::SUCCESS;
    }
}
