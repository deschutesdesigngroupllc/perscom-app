<?php

declare(strict_types=1);

namespace App\Actions\Batches\Central;

use App\Jobs\Tenant\OptimizeDatabase;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CreateOptimizeTenantDatabasesBatch
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): OptimizeDatabase => new OptimizeDatabase(
                tenantKey: $tenant->getKey()
            ))
        )->name(
            name: 'Optimize Tenant Database'
        )->onQueue(
            queue: 'clean'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
