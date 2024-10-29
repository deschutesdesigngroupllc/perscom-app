<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Central\SendRecurringMessages;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ScheduleRecurringMessages
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant) => new SendRecurringMessages($tenant->getKey()))
        )->name(
            name: 'Recurring Messages'
        )->onQueue(
            queue: 'default'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
