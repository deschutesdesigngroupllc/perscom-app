<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Central\CalculateSchedules as CalculateSchedulesJob;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CalculateSchedules
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant) => new CalculateSchedulesJob($tenant->getKey()))
        )->name(
            name: 'Schedule Calculations'
        )->onQueue(
            queue: 'default'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
