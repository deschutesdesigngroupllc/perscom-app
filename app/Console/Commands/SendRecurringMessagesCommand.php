<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\SendRecurringMessages;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Description('Send any recurring messages.')]
#[Signature('perscom:recurring-messages')]
class SendRecurringMessagesCommand extends Command
{
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
