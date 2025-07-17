<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\OptimizeTenantDatabases;
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
            return 1;
        }

        OptimizeTenantDatabases::handle();

        $this->info('The optimize database jobs have been dispatched.');

        return static::SUCCESS;
    }
}
