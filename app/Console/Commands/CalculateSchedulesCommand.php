<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\CalculateSchedules;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Description('Calculates the next occurrence of a schedule and updates the database record.')]
#[Signature('perscom:calculate-schedules')]
class CalculateSchedulesCommand extends Command
{
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
