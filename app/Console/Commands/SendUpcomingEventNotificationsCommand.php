<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\SendUpcomingEventNotifications;
use Illuminate\Console\Command;
use Throwable;

class SendUpcomingEventNotificationsCommand extends Command
{
    protected $signature = 'perscom:event-notifications';

    protected $description = 'Send any upcoming event notifications.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(SendUpcomingEventNotifications::class, name: 'Send Upcoming Event Notifications');

        $this->components->info('The event notification job has been dispatched.');

        return static::SUCCESS;
    }
}
