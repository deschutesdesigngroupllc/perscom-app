<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Tenant\SendUpcomingEventNotifications;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class SendTenantUpcomingEventNotifications
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): SendUpcomingEventNotifications => new SendUpcomingEventNotifications(
                tenantKey: $tenant->getKey()
            ))
        )->name(
            name: 'Send Tenant Upcoming Event Notifications'
        )->onQueue(
            queue: 'default'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
