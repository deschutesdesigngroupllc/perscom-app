<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Events\Automations\ScheduledTriggerFired;
use App\Jobs\Concerns\RunsForTenant;
use App\Models\Automation;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Date;

class RunDailyUserReview implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy();
    }

    /**
     * Build the additional automation context for a user's daily review.
     *
     * @return array{time_in_grade_days: int|null, time_in_service_days: int|null, days_since_last_seen: int|null, events_attended_30d: int, events_missed_30d: int, qualification_ids: array<int, int>}
     */
    public static function contextFor(User $user): array
    {
        $lastRankChange = $user->rank_records()->value('created_at');

        return [
            'time_in_grade_days' => filled($lastRankChange) ? (int) Date::parse($lastRankChange)->diffInDays(now()) : null,
            'time_in_service_days' => $user->created_at ? (int) $user->created_at->diffInDays(now()) : null,
            'days_since_last_seen' => $user->last_seen_at ? (int) $user->last_seen_at->diffInDays(now()) : null,
            'events_attended_30d' => static::countRecentRegistrations($user, EventRegistrationStatus::ATTENDED),
            'events_missed_30d' => static::countRecentRegistrations($user, EventRegistrationStatus::NO_SHOW),
            'qualification_ids' => $user->qualification_records()
                ->where(fn (Builder $query): Builder => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->pluck('qualification_id')
                ->unique()
                ->map(fn (mixed $id): int => (int) $id)
                ->values()
                ->all(),
        ];
    }

    public function work(): void
    {
        if (! Automation::query()->enabled()->forTrigger(AutomationTrigger::USER_DAILY_REVIEW)->exists()) {
            return;
        }

        User::query()->chunkById(100, fn (Collection $users) => $users->each(fn (User $user) => event(new ScheduledTriggerFired(
            subject: $user,
            trigger: AutomationTrigger::USER_DAILY_REVIEW,
            additionalContext: static::contextFor($user),
        ))));
    }

    protected static function countRecentRegistrations(User $user, EventRegistrationStatus $status): int
    {
        return $user->eventRegistrations()
            ->where('status', $status->value)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
    }
}
