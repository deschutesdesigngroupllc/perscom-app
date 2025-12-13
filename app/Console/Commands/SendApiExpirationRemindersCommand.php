<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\Central\CreateApiExpirationRemindersBatch;
use Illuminate\Console\Command;
use Throwable;

class SendApiExpirationRemindersCommand extends Command
{
    protected $signature = 'perscom:api-expiration-reminders';

    protected $description = 'Send any API expiration reminders.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        CreateApiExpirationRemindersBatch::handle();

        $this->components->info('The API expiration reminders job has been dispatched.');

        return static::SUCCESS;
    }
}
