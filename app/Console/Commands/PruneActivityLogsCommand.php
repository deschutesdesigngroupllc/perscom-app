<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\PurgeTenantActivityLogs;
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
            return 1;
        }

        PurgeTenantActivityLogs::handle();

        $this->info('The purge activity logs job has been dispatched to the queue.');

        return static::SUCCESS;
    }
}
