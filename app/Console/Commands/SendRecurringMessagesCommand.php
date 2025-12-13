<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\Central\CreateSendTenantRecurringMessagesBatch;
use Illuminate\Console\Command;
use Throwable;

class SendRecurringMessagesCommand extends Command
{
    protected $signature = 'perscom:recurring-messages';

    protected $description = 'Send any recurring messages.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        CreateSendTenantRecurringMessagesBatch::handle();

        $this->components->info('The recurring messages job has been dispatched.');

        return static::SUCCESS;
    }
}
