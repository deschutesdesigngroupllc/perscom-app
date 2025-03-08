<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class PruneLogsCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'perscom:prune
                            {--tenants=* : The tenant(s) to run the command for. Default: all}
                            {--days= : (optional) Records older than this number of days will be cleaned.}
                            {--force : (optional) Force the operation to run when in production.}';

    protected $description = 'Prunes the various logs for each tenant.';

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $tenants = collect($this->option('tenants'));

        tenancy()->runForMultiple($tenants, function ($tenant): void {
            $this->line("Tenant: {$tenant->getTenantKey()}");

            $this->call('activitylog:clean', [
                '--days' => $this->option('days'),
                '--force' => true,
            ]);
        });

        return static::SUCCESS;
    }
}
