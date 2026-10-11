<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\ProcessExpiringRecords;
use App\Jobs\Tenant\RunDailyUserReview;
use Illuminate\Console\Command;
use Throwable;

class RunScheduledAutomationsCommand extends Command
{
    protected $signature = 'perscom:scheduled-automations';

    protected $description = 'Process expiring records and run the daily user review automations.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(ProcessExpiringRecords::class, name: 'Process Expiring Records');
        DispatchTenantJob::for(RunDailyUserReview::class, name: 'Run Daily User Review');

        $this->components->info('The scheduled automation jobs have been dispatched.');

        return static::SUCCESS;
    }
}
