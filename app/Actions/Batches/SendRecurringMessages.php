<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\Central\SendRecurringMessages as SendRecurringMessagesJob;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Throwable;

class SendRecurringMessages
{
    /**
     * @throws Throwable
     */
    public static function handle(): Batch
    {
        return Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant|Model $tenant): SendRecurringMessagesJob => new SendRecurringMessagesJob($tenant->getKey()))
        )->name(
            name: 'Recurring Messages'
        )->onQueue(
            queue: 'default'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
