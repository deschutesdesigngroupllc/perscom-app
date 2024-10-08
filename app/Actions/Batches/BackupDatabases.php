<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Central\BackupTenantDatabase;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class BackupDatabases
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->each(fn (Tenant $tenant) => new BackupTenantDatabase($tenant->getKey()))
        )->name(
            name: 'Database Backups'
        )->onQueue(
            queue: 'backup'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
