<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\SendRecurringMessages as SendRecurringMessagesAction;
use Illuminate\Console\Command;
use Throwable;

class SendRecurringMessages extends Command
{
    protected $signature = 'perscom:recurring-messages';

    protected $description = 'Send any recurring messages.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        SendRecurringMessagesAction::handle();

        $this->info('The recurring messages job has been dispatched.');

        return static::SUCCESS;
    }
}
