<?php

declare(strict_types=1);

namespace App\Actions\Batches\Central;

use App\Jobs\Tenant\SendApiExpirationReminders;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CreateApiExpirationRemindersBatch
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): SendApiExpirationReminders => new SendApiExpirationReminders(
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
