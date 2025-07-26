<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\PruneTenantApiLogs;
use App\Actions\Batches\PruneTenantApiPurgeLogs;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Throwable;

class PruneActivityLogsCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'perscom:prune
                            {--days= : (optional) Records older than this number of days will be cleaned.}
                            {--force : (optional) Force the operation to run when in production.}';

    protected $description = 'Prunes the various logs for each tenant.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $days = intval($this->option('days') ?? 30);

        if ($days < 0) {
            $this->error('The days argument must not be negative.');

            return Command::FAILURE;
        }

        PruneTenantApiLogs::handle(
            days: $days,
        );

        PruneTenantApiPurgeLogs::handle(
            days: $days,
        );

        $this->info('The prune activity logs job has been dispatched to the queue.');

        return static::SUCCESS;
    }
}
