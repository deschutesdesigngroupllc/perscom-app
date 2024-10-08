<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\SendUpcomingEventNotifications as SendUpcomingEventNotificationsAction;
use Illuminate\Console\Command;
use Throwable;

class SendUpcomingEventNotifications extends Command
{
    protected $signature = 'perscom:event-notification';

    protected $description = 'Send any upcoming event notifications.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        SendUpcomingEventNotificationsAction::handle();

        $this->info('The event notification job has been dispatched.');

        return static::SUCCESS;
    }
}
