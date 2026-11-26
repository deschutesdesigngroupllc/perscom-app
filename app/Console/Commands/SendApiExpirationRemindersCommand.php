<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\SendApiExpirationReminders;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Description('Send any API expiration reminders.')]
#[Signature('perscom:api-expiration-reminders')]
class SendApiExpirationRemindersCommand extends Command
{
    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(SendApiExpirationReminders::class, name: 'Send API Expiration Reminders');

        $this->components->info('The API expiration reminders job has been dispatched.');

        return static::SUCCESS;
    }
}
