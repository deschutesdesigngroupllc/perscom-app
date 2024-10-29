<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Central\SendUpcomingEventNotifications;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ScheduleUpcomingEventNotifications
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant) => new SendUpcomingEventNotifications($tenant->getKey()))
        )->name(
            name: 'Upcoming Event Notifications'
        )->onQueue(
            queue: 'default'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
