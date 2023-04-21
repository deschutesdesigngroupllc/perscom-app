<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Arr;

class PruneLogs extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perscom:prune
                            {--tenants=* : The tenant(s) to run the command for. Default: all}
                            {--days= : (optional) Records older than this number of days will be cleaned.}
                            {--force : (optional) Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prunes the various logs for each tenant.';

    /**
     * Execute the console command.
     */
    public function handle()
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

        return Command::SUCCESS;
    }
}
