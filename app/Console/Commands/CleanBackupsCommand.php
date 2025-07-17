<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\CleanTenantBackups;
use App\Jobs\Central\CleanBackups;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Throwable;

class CleanBackupsCommand extends Command implements Isolatable
{
    protected $signature = 'perscom:backup-clean';

    protected $description = 'Clean up the all application backups.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        CleanTenantBackups::handle();

        CleanBackups::dispatch();

        $this->info('The database cleanup jobs have been dispatched.');

        return static::SUCCESS;
    }
}
