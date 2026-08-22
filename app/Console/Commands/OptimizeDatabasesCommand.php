<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\OptimizeDatabase;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\Isolatable;
use Throwable;

class OptimizeDatabasesCommand extends Command implements Isolatable
{
    use ConfirmableTrait;

    protected $signature = 'perscom:optimize
                            {--force : (optional) Force the operation to run when in production.}';

    protected $description = 'Optimizes all database tables.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        DispatchTenantJob::for(OptimizeDatabase::class, name: 'Optimize Tenant Databases', queue: 'clean');

        $this->components->info('The optimize database jobs have been dispatched.');

        return static::SUCCESS;
    }
}
