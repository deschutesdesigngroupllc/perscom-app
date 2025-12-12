<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\Central\CreateSendUpcomingEventNotificationsBatch;
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
        CreateSendUpcomingEventNotificationsBatch::handle();

        $this->info('The event notification job has been dispatched.');

        return static::SUCCESS;
    }
}
