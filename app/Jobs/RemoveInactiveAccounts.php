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

    public function __construct()
    {
        $this->onQueue('system');
    }

    public function handle(): void
    {
        // @phpstan-ignore-next-line
        Tenant::all()->each(function (Tenant $tenant) {
            $dateToCompare = match (true) {
                isset($tenant->last_login_at) => $tenant->last_login_at,
                default => $tenant->created_at
            };

            if ($dateToCompare?->isSameDay(now()->subMonths(5))) {
                $tenant->notify(new DeleteAccountOneMonth());

                Log::debug('Inactive account one month deletion warning sent', ['tenant' => $tenant]);

                return true;
            }

            if ($dateToCompare?->isSameDay(now()->subMonths(6)->addWeek())) {
                $tenant->notify(new DeleteAccountOneWeek());

                Log::debug('Inactive account one week deletion warning sent', ['tenant' => $tenant]);

                return true;
            }

            if ($dateToCompare?->isSameDay(now()->subMonths(6))) {
                $tenant->notifyNow(new DeleteAccount());
                $tenant->delete();

                Log::debug('Inactive account deleted', ['tenant' => $tenant]);

                return true;
            }
        });
    }

    /**
     * @return int[]
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    public function failed(mixed $exception): void
    {
        Log::error('Failed to remove inactive accounts', [
            'exception' => $exception,
        ]);
    }
}
