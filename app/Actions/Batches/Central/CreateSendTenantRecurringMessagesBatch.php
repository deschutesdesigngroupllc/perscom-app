<?php

declare(strict_types=1);

namespace App\Actions\Batches\Central;

use App\Jobs\Tenant\SendRecurringMessages;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CreateSendTenantRecurringMessagesBatch
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): SendRecurringMessages => new SendRecurringMessages(
                tenantKey: $tenant->getKey()
            ))
        )->name(
            name: 'Send Tenant Recurring Messages'
        )->onQueue(
            queue: 'default'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
