<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Notifications\System\DeleteAccount;
use App\Notifications\System\DeleteAccountOneMonth;
use App\Notifications\System\DeleteAccountOneWeek;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveInactiveAccounts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Tenant::all()->each(function (Tenant $tenant) {
            if ($tenant->last_login_at->isSameDay(now()->subMonths(5))) {
                $tenant->notify(new DeleteAccountOneMonth());

                Log::debug('Inactive account one month deletion warning sent', ['tenant' => $tenant]);

                return true;
            }

            if ($tenant->last_login_at->isSameDay(now()->subMonths(6)->addWeek())) {
                $tenant->notify(new DeleteAccountOneWeek());

                Log::debug('Inactive account one week deletion warning sent', ['tenant' => $tenant]);

                return true;
            }

            if ($tenant->last_login_at->isSameDay(now()->subMonths(6))) {
                $tenant->notifyNow(new DeleteAccount());
                $tenant->delete();

                Log::debug('Inactive account deleted', ['tenant' => $tenant]);

                return true;
            }
        });
    }
}
