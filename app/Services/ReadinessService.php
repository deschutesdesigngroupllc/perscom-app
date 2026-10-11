<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Readiness\UnitReadinessData;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ReadinessService
{
    /**
     * The window, in days, used for attendance rates.
     */
    public const int ATTENDANCE_WINDOW_DAYS = 30;

    /**
     * The window, in days, used for upcoming expirations.
     */
    public const int EXPIRING_WINDOW_DAYS = 30;

    /**
     * Readiness metrics for the whole organization followed by each unit with members.
     *
     * @return Collection<int, UnitReadinessData>
     */
    public static function summaries(): Collection
    {
        $members = User::query()->select(['id', 'unit_id'])->get();
        $notReady = static::notReadyUserIds();
        $attendance = static::attendanceByUser();
        $expiring = static::expiringItems()->countBy('user_id');

        $summarize = fn (?Unit $unit, string $label, EloquentCollection $users): UnitReadinessData => new UnitReadinessData(
            unit: $unit,
            label: $label,
            members: $users->count(),
            readyPercentage: $users->isEmpty()
                ? null
                : round(100 * $users->reject(fn (User $user): bool => $notReady->contains($user->getKey()))->count() / $users->count(), 1),
            attendanceRate: static::attendanceRate($users->modelKeys(), $attendance),
            expiring: (int) $users->sum(fn (User $user): int => (int) $expiring->get($user->getKey(), 0)),
        );

        $units = Unit::query()->whereIn('id', $members->pluck('unit_id')->filter()->unique())->get();

        return $units
            ->map(fn (Unit $unit): UnitReadinessData => $summarize($unit, $unit->name, $members->where('unit_id', $unit->getKey())))
            ->prepend($summarize(null, 'Organization', $members))
            ->values();
    }

    /**
     * Members holding an expired qualification or credential that has not been renewed.
     *
     * @return Collection<int, int>
     */
    public static function notReadyUserIds(): Collection
    {
        $qualifications = DB::table('records_qualifications')
            ->whereNotNull('expires_at')
            ->select(['user_id', 'qualification_id as item_id', 'expires_at'])
            ->get();

        $credentials = DB::table('records_trainings_credentials')
            ->join('records_trainings', 'records_trainings.id', '=', 'records_trainings_credentials.training_record_id')
            ->whereNotNull('records_trainings_credentials.expires_at')
            ->select(['records_trainings.user_id', 'records_trainings_credentials.credential_id as item_id', 'records_trainings_credentials.expires_at'])
            ->get();

        $currentQualifications = DB::table('records_qualifications')
            ->where(fn (QueryBuilder $query): QueryBuilder => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->get(['user_id', 'qualification_id as item_id'])
            ->map(fn (object $row): string => $row->user_id.':'.$row->item_id);

        $currentCredentials = DB::table('records_trainings_credentials')
            ->join('records_trainings', 'records_trainings.id', '=', 'records_trainings_credentials.training_record_id')
            ->where(fn (QueryBuilder $query): QueryBuilder => $query->whereNull('records_trainings_credentials.expires_at')->orWhere('records_trainings_credentials.expires_at', '>', now()))
            ->get(['records_trainings.user_id', 'records_trainings_credentials.credential_id as item_id'])
            ->map(fn (object $row): string => $row->user_id.':'.$row->item_id);

        $lapsed = fn (Collection $rows, Collection $current): Collection => $rows
            ->filter(fn (object $row): bool => Date::parse($row->expires_at)->lessThanOrEqualTo(now()))
            ->reject(fn (object $row): bool => $current->contains($row->user_id.':'.$row->item_id))
            ->pluck('user_id');

        return $lapsed($qualifications, $currentQualifications)
            ->merge($lapsed($credentials, $currentCredentials))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
    }

    /**
     * Qualifications and credentials expiring within the window, soonest first.
     *
     * @return Collection<array-key, array{key: string, type: string, user_id: int, member: string, item: string, expires_at: Carbon}>
     */
    public static function expiringItems(int $days = self::EXPIRING_WINDOW_DAYS): Collection
    {
        $window = [now(), now()->addDays($days)];

        $qualifications = DB::table('records_qualifications')
            ->join('users', 'users.id', '=', 'records_qualifications.user_id')
            ->join('qualifications', 'qualifications.id', '=', 'records_qualifications.qualification_id')
            ->whereBetween('records_qualifications.expires_at', $window)
            ->get(['records_qualifications.id', 'users.id as user_id', 'users.name as member', 'qualifications.name as item', 'records_qualifications.expires_at'])
            ->map(fn (object $row): array => [
                'key' => 'qualification-'.$row->id,
                'type' => 'Qualification',
                'user_id' => (int) $row->user_id,
                'member' => (string) $row->member,
                'item' => (string) $row->item,
                'expires_at' => Date::parse($row->expires_at),
            ]);

        $credentials = DB::table('records_trainings_credentials')
            ->join('records_trainings', 'records_trainings.id', '=', 'records_trainings_credentials.training_record_id')
            ->join('users', 'users.id', '=', 'records_trainings.user_id')
            ->join('credentials', 'credentials.id', '=', 'records_trainings_credentials.credential_id')
            ->whereBetween('records_trainings_credentials.expires_at', $window)
            ->get(['records_trainings_credentials.id', 'users.id as user_id', 'users.name as member', 'credentials.name as item', 'records_trainings_credentials.expires_at'])
            ->map(fn (object $row): array => [
                'key' => 'credential-'.$row->id,
                'type' => 'Credential',
                'user_id' => (int) $row->user_id,
                'member' => (string) $row->member,
                'item' => (string) $row->item,
                'expires_at' => Date::parse($row->expires_at),
            ]);

        return $qualifications
            ->merge($credentials)
            ->sortBy('expires_at')
            ->keyBy('key');
    }

    /**
     * Attended and no-show counts per user within the attendance window.
     *
     * @return Collection<int, array{attended: int, no_show: int}>
     */
    protected static function attendanceByUser(): Collection
    {
        return DB::table('events_registrations')
            ->whereIn('status', [EventRegistrationStatus::ATTENDED->value, EventRegistrationStatus::NO_SHOW->value])
            ->whereRaw('COALESCE(checked_in_at, updated_at) >= ?', [now()->subDays(static::ATTENDANCE_WINDOW_DAYS)])
            ->groupBy('user_id', 'status')
            ->selectRaw('user_id, status, COUNT(*) as total')
            ->get()
            ->groupBy('user_id')
            ->map(fn (Collection $rows): array => [
                'attended' => (int) $rows->firstWhere('status', EventRegistrationStatus::ATTENDED->value)?->total,
                'no_show' => (int) $rows->firstWhere('status', EventRegistrationStatus::NO_SHOW->value)?->total,
            ]);
    }

    /**
     * @param  array<int, int>  $userIds
     * @param  Collection<int, array{attended: int, no_show: int}>  $attendance
     */
    protected static function attendanceRate(array $userIds, Collection $attendance): ?float
    {
        $totals = $attendance->only($userIds);
        $attended = $totals->sum('attended');
        $recorded = $attended + $totals->sum('no_show');

        return $recorded === 0 ? null : round(100 * $attended / $recorded, 1);
    }
}
