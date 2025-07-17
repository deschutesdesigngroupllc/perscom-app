<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Tenant\PurgeActivityLogs;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class PurgeTenantActivityLogs
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): PurgeActivityLogs => new PurgeActivityLogs(
                tenantKey: $tenant->getKey(),
            ))
        )->name(
            name: 'Purge Tenant Activity Logs'
        )->onQueue(
            queue: 'clean'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
