<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PruneLogs extends Command
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

        $tenants = Arr::wrap($this->option('tenants'));

        // @phpstan-ignore-next-line
        tenancy()->runForMultiple($tenants, function ($tenant) {
            $this->line("Tenant: {$tenant->getTenantKey()}");

            $this->call('activitylog:clean', [
                '--days' => $this->option('days'),
                '--force' => true,
            ]);
        });

        return CommandAlias::SUCCESS;
    }
}
