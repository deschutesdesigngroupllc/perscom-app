<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\SendUpcomingEventNotifications;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Description('Send any upcoming event notifications.')]
#[Signature('perscom:event-notifications')]
class SendUpcomingEventNotificationsCommand extends Command
{
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
