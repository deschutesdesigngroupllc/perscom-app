<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\BackupTenantDatabase;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class BackupDatabase
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant $tenant) => new BackupTenantDatabase($tenant->getKey()))
        )->name(
            name: 'Database Backups'
        )->onQueue(
            queue: 'backup'
        )->dispatch();
    }
}
