<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\SendRecurringMessages;
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
        DispatchTenantJob::for(SendRecurringMessages::class, name: 'Send Recurring Messages');

        $this->components->info('The recurring messages job has been dispatched.');

        return static::SUCCESS;
    }
}
