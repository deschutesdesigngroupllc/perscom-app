<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Central\CleanBackups;
use App\Jobs\Tenant\CleanBackups as CleanTenantBackups;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Throwable;

#[Description('Clean up the all application backups.')]
#[Signature('perscom:backup-clean')]
class CleanBackupsCommand extends Command implements Isolatable
{
    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(CleanTenantBackups::class, name: 'Clean Tenant Backups', queue: 'clean');

        if (config('tenancy.enabled')) {
            dispatch(new CleanBackups);
        }

        $this->components->info('The database cleanup jobs have been dispatched.');

        return static::SUCCESS;
    }
}
