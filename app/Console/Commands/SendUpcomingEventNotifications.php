<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendUpcomingEventNotifications as SendUpcomingEventNotificationJob;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class SendUpcomingEventNotifications extends Command
{
    protected $signature = 'perscom:event-notifications';

    protected $description = 'Send any upcoming event notifications.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        Bus::batch(
            jobs: Tenant::all()->map(fn (Tenant $tenant) => new SendUpcomingEventNotificationJob($tenant->getKey()))
        )->name(
            name: 'Upcoming Event Notifications'
        )->onQueue(
            queue: 'default'
        )->dispatch();

        $this->info('The event notification job has been dispatched.');

        return static::SUCCESS;
    }
}
