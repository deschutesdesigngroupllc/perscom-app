<?php

declare(strict_types=1);

namespace App\Jobs\System;

use App\Contracts\RequiresTenancy;
use App\Models\Tenant;
use App\Notifications\System\DeleteAccount;
use App\Notifications\System\DeleteAccountOneDay;
use App\Notifications\System\DeleteAccountOneMonth;
use App\Notifications\System\DeleteAccountOneWeek;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;
use Throwable;

class RemoveInactiveAccounts implements RequiresTenancy, ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('system');
    }

    /**
     * The date inactivity is measured from: when a canceled subscription ends, else the last sign-in,
     * else when the organization was created.
     */
    public static function lastActivityDate(Tenant $tenant): ?CarbonInterface
    {
        return match (true) {
            $tenant->subscription()?->ends_at !== null => $tenant->subscription()->ends_at,
            $tenant->last_login_at !== null => $tenant->last_login_at,
            default => $tenant->created_at
        };
    }

    /**
     * The day an inactive organization will be deleted, two months after its last activity.
     */
    public static function deletionDate(Tenant $tenant): string
    {
        return (RemoveInactiveAccounts::lastActivityDate($tenant) ?? now())->copy()->addMonths(2)->toFormattedDateString();
    }

    /**
     * A paying organization is never treated as inactive, however long it has been since anyone signed in.
     */
    public static function hasActiveSubscription(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription();

        return $subscription instanceof Subscription && $subscription->ends_at === null && $subscription->active();
    }

    public function handle(): void
    {
        Tenant::all()->each(function (Tenant $tenant): void {
            if (RemoveInactiveAccounts::hasActiveSubscription($tenant)) {
                return;
            }

            $dateToCompare = RemoveInactiveAccounts::lastActivityDate($tenant);

            if ($dateToCompare?->isSameDay(now()->subMonth())) {
                $tenant->notify(new DeleteAccountOneMonth);
                Log::debug('Inactive account one month deletion warning sent', ['tenant' => $tenant]);
            }

            if ($dateToCompare?->isSameDay(now()->subMonths(2)->addWeek())) {
                $tenant->notify(new DeleteAccountOneWeek);
                Log::debug('Inactive account one week deletion warning sent', ['tenant' => $tenant]);
            }

            if ($dateToCompare?->isSameDay(now()->subMonths(2)->addDay())) {
                $tenant->notify(new DeleteAccountOneDay);
                Log::debug('Inactive account one day deletion warning sent', ['tenant' => $tenant]);
            }

            if ($dateToCompare?->isSameDay(now()->subMonths(2))) {
                $tenant->notifyNow(new DeleteAccount);
                $tenant->delete();
                Log::debug('Inactive account deleted', ['tenant' => $tenant]);
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

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to remove inactive accounts', [
            'exception' => $exception,
        ]);
    }
}
