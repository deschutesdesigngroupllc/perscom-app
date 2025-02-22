<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Central\CleanTenantBackups;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CleanBackups
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant) => new CleanTenantBackups($tenant->getKey()))
        )->name(
            name: 'Clean Backups'
        )->onQueue(
            queue: 'backup'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
