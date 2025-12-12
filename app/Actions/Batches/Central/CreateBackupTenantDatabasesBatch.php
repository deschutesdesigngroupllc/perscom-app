<?php

declare(strict_types=1);

namespace App\Actions\Batches\Central;

use App\Jobs\Tenant\BackupDatabase;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CreateBackupTenantDatabasesBatch
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): BackupDatabase => new BackupDatabase(
                tenantKey: $tenant->getKey()
            ))
        )->name(
            name: 'Backup Tenant Databases'
        )->onQueue(
            queue: 'backup'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
