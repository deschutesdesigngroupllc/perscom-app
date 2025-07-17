<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Tenant\PruneApiLogs;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class PruneTenantApiLogs
{
    /**
     * @throws Throwable
     */
    public static function handle(int $days = 30): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): PruneApiLogs => new PruneApiLogs(
                tenantKey: $tenant->getKey(),
                days: $days,
            ))
        )->name(
            name: 'Prune Tenant API Logs'
        )->onQueue(
            queue: 'clean'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
