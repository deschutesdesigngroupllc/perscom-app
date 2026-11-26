<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\NotifyPromotionEligibleMembers;
use App\Jobs\Tenant\ProcessExpiringRecords;
use App\Jobs\Tenant\RunDailyUserReview;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Description('Process expiring records, run the daily user review automations and notify members newly eligible for promotion.')]
#[Signature('perscom:scheduled-automations')]
class RunScheduledAutomationsCommand extends Command
{
    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(ProcessExpiringRecords::class, name: 'Process Expiring Records');
        DispatchTenantJob::for(RunDailyUserReview::class, name: 'Run Daily User Review');
        DispatchTenantJob::for(NotifyPromotionEligibleMembers::class, name: 'Notify Promotion Eligible Members');

        $this->components->info('The scheduled automation jobs have been dispatched.');

        return static::SUCCESS;
    }
}
