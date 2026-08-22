<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\SendApiExpirationReminders;
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
        DispatchTenantJob::for(SendApiExpirationReminders::class, name: 'Send API Expiration Reminders');

        $this->components->info('The API expiration reminders job has been dispatched.');

        return static::SUCCESS;
    }
}
