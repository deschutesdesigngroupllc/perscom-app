<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Tenant\CleanBackups;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CleanTenantBackups
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): CleanBackups => new CleanBackups(
                tenantKey: $tenant->getKey()
            ))
        )->name(
            name: 'Clean Tenant Backups'
        )->onQueue(
            queue: 'backup'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
