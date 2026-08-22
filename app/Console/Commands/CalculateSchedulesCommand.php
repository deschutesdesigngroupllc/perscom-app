<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\CalculateSchedules;
use Illuminate\Console\Command;
use Throwable;

class CalculateSchedulesCommand extends Command
{
    protected $signature = 'perscom:calculate-schedules';

    protected $description = 'Calculates the next occurrence of a schedule and updates the database record.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(CalculateSchedules::class, name: 'Calculate Schedules');

        $this->components->info('The schedule job has been dispatched.');

        return static::SUCCESS;
    }
}
