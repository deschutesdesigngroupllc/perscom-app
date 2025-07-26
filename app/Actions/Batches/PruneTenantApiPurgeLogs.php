<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Tenant\PruneApiPurgeLogs;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class PruneTenantApiPurgeLogs
{
    /**
     * @throws Throwable
     */
    public static function handle(int $days = 30): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): PruneApiPurgeLogs => new PruneApiPurgeLogs(
                tenantKey: $tenant->getKey(),
                days: $days,
            ))
        )->name(
            name: 'Prune Tenant API Purge Logs'
        )->onQueue(
            queue: 'clean'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
