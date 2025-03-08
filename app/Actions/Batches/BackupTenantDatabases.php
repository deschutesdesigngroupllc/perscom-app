<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Central\BackupTenantDatabase;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class BackupTenantDatabases
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): BackupTenantDatabase => new BackupTenantDatabase($tenant->getKey()))
        )->name(
            name: 'Database Backups'
        )->onQueue(
            queue: 'backup'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
