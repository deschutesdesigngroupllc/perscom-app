<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\PruneApiLogs;
use App\Jobs\Tenant\PruneApiPurgeLogs;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Throwable;

#[Description('Prunes the various logs for each tenant.')]
#[Signature('perscom:prune
                            {--days= : (optional) Records older than this number of days will be cleaned.}
                            {--force : (optional) Force the operation to run when in production.}')]
class PruneActivityLogsCommand extends Command
{
    use ConfirmableTrait;

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $days = (int) ($this->option('days') ?? 30);

        if ($days < 0) {
            $this->components->error('The days argument must not be negative.');

            return Command::FAILURE;
        }

        DispatchTenantJob::for(PruneApiLogs::class, name: 'Prune API Logs', queue: 'clean', arguments: [$days]);

        DispatchTenantJob::for(PruneApiPurgeLogs::class, name: 'Prune API Purge Logs', queue: 'clean', arguments: [$days]);

        $this->components->info('The prune activity logs job has been dispatched to the queue.');

        return static::SUCCESS;
    }
}
