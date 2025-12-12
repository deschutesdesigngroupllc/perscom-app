<?php

declare(strict_types=1);

namespace App\Actions\Batches\Central;

use App\Jobs\Tenant\CalculateSchedules;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CreateCalculateTenantSchedulesBatch
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): CalculateSchedules => new CalculateSchedules(
                tenantKey: $tenant->getKey()
            ))
        )->name(
            name: 'Calculate Tenant Schedules'
        )->onQueue(
            queue: 'default'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
