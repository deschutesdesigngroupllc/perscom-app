<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Promotions\PromotionEvaluationData;
use App\Data\Promotions\RequirementResultData;
use App\Models\Credential;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\RankRequirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class PromotionEligibilityService
{
    /**
     * The window, in days, used when counting attended events.
     */
    public const int ATTENDANCE_WINDOW_DAYS = 90;

    /**
     * Ranks are ordered most senior first, so the next rank is the one directly before the current rank.
     */
    public static function nextRankFor(Rank $rank, ?Collection $ranks = null): ?Rank
    {
        $ranks = ($ranks ?? static::ranks())->values();

        $index = $ranks->search(fn (Rank $candidate): bool => $candidate->is($rank));

        if ($index === false || $index === 0) {
            return null;
        }

        return $ranks->get($index - 1);
    }

    public static function evaluate(User $user): PromotionEvaluationData
    {
        return static::evaluateMany(new EloquentCollection([$user]))->first();
    }

    /**
     * Evaluate a batch of users with a fixed number of queries regardless of batch size.
     *
     * @param  EloquentCollection<int, User>  $users
     * @return Collection<int, PromotionEvaluationData>
     */
    public static function evaluateMany(EloquentCollection $users, ?Collection $ranks = null): Collection
    {
        $ranks ??= static::ranks();
        $userIds = $users->modelKeys();

        $lastRankChanges = DB::table('records_ranks')
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->selectRaw('user_id, MAX(created_at) as last_rank_change')
            ->pluck('last_rank_change', 'user_id');

        $attendance = DB::table('events_registrations')
            ->whereIn('user_id', $userIds)
            ->where('status', EventRegistrationStatus::ATTENDED->value)
            ->whereRaw('COALESCE(checked_in_at, updated_at) >= ?', [now()->subDays(static::ATTENDANCE_WINDOW_DAYS)])
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as attended')
            ->pluck('attended', 'user_id');

        $qualifications = DB::table('records_qualifications')
            ->whereIn('user_id', $userIds)
            ->where(fn (QueryBuilder $query): QueryBuilder => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->select(['user_id', 'qualification_id'])
            ->get()
            ->groupBy('user_id')
            ->map(fn (Collection $rows): array => $rows->pluck('qualification_id')->map(fn (mixed $id): int => (int) $id)->unique()->values()->all());

        $credentials = DB::table('records_trainings_credentials')
            ->join('records_trainings', 'records_trainings.id', '=', 'records_trainings_credentials.training_record_id')
            ->whereIn('records_trainings.user_id', $userIds)
            ->where(fn (QueryBuilder $query): QueryBuilder => $query->whereNull('records_trainings_credentials.expires_at')->orWhere('records_trainings_credentials.expires_at', '>', now()))
            ->select(['records_trainings.user_id', 'records_trainings_credentials.credential_id'])
            ->get()
            ->groupBy('user_id')
            ->map(fn (Collection $rows): array => $rows->pluck('credential_id')->map(fn (mixed $id): int => (int) $id)->unique()->values()->all());

        $requirements = $ranks->pluck('requirement')->filter();
        $qualificationNames = Qualification::query()
            ->whereIn('id', $requirements->pluck('required_qualification_ids')->flatten()->filter()->unique())
            ->pluck('name', 'id');
        $credentialNames = Credential::query()
            ->whereIn('id', $requirements->pluck('required_credential_ids')->flatten()->filter()->unique())
            ->pluck('name', 'id');

        return $users->mapWithKeys(function (User $user) use ($ranks, $lastRankChanges, $attendance, $qualifications, $credentials, $qualificationNames, $credentialNames): array {
            $currentRank = $ranks->firstWhere('id', $user->rank_id);

            if (! $currentRank instanceof Rank) {
                return [$user->getKey() => new PromotionEvaluationData($user, null, null, false, reason: 'The member does not have a rank.')];
            }

            $nextRank = static::nextRankFor($currentRank, $ranks);

            if (! $nextRank instanceof Rank) {
                return [$user->getKey() => new PromotionEvaluationData($user, $currentRank, null, false, reason: 'The member holds the most senior rank.')];
            }

            $requirement = $nextRank->requirement;

            if (! $requirement instanceof RankRequirement || ! $requirement->hasRequirements()) {
                return [$user->getKey() => new PromotionEvaluationData($user, $currentRank, $nextRank, false, reason: 'No promotion requirements are configured for the next rank.')];
            }

            $lastRankChange = $lastRankChanges->get($user->getKey());
            $timeInGradeFrom = filled($lastRankChange) ? Date::parse($lastRankChange) : $user->created_at;

            $results = static::evaluateRequirement(
                requirement: $requirement,
                daysInGrade: $timeInGradeFrom ? (int) $timeInGradeFrom->diffInDays(now()) : 0,
                daysInService: $user->created_at ? (int) $user->created_at->diffInDays(now()) : 0,
                eventsAttended: (int) $attendance->get($user->getKey(), 0),
                heldQualificationIds: $qualifications->get($user->getKey(), []),
                heldCredentialIds: $credentials->get($user->getKey(), []),
                qualificationNames: $qualificationNames,
                credentialNames: $credentialNames,
            );

            $eligible = collect($results)->every(fn (RequirementResultData $result): bool => $result->met);

            return [$user->getKey() => new PromotionEvaluationData($user, $currentRank, $nextRank, $eligible, $results)];
        });
    }

    /**
     * Evaluate every ranked member, optionally limited to a unit.
     *
     * @return Collection<int, PromotionEvaluationData>
     */
    public static function evaluateAll(?int $unitId = null): Collection
    {
        $ranks = static::ranks();
        $evaluations = collect();

        User::query()
            ->whereNotNull('rank_id')
            ->when(filled($unitId), fn (Builder $query): Builder => $query->where('unit_id', $unitId))
            ->chunkById(500, function (EloquentCollection $users) use ($ranks, &$evaluations): void {
                $evaluations = $evaluations->union(static::evaluateMany($users, $ranks));
            });

        return $evaluations;
    }

    /**
     * @return Collection<int, PromotionEvaluationData>
     */
    public static function eligible(?int $unitId = null): Collection
    {
        return static::evaluateAll($unitId)->filter(fn (PromotionEvaluationData $evaluation): bool => $evaluation->eligible);
    }

    /**
     * @return Collection<int, PromotionEvaluationData>
     */
    public static function notYetEligible(?int $unitId = null): Collection
    {
        return static::evaluateAll($unitId)->filter(fn (PromotionEvaluationData $evaluation): bool => ! $evaluation->eligible && $evaluation->nextRank instanceof Rank);
    }

    /**
     * @return EloquentCollection<int, Rank>
     */
    public static function ranks(): EloquentCollection
    {
        return Rank::query()->with('requirement')->get();
    }

    /**
     * @param  array<int, int>  $heldQualificationIds
     * @param  array<int, int>  $heldCredentialIds
     * @param  Collection<int, string>  $qualificationNames
     * @param  Collection<int, string>  $credentialNames
     * @return array<int, RequirementResultData>
     */
    protected static function evaluateRequirement(
        RankRequirement $requirement,
        int $daysInGrade,
        int $daysInService,
        int $eventsAttended,
        array $heldQualificationIds,
        array $heldCredentialIds,
        Collection $qualificationNames,
        Collection $credentialNames,
    ): array {
        return collect([
            filled($requirement->min_days_in_grade) ? new RequirementResultData(
                label: 'Time in grade',
                met: $daysInGrade >= $requirement->min_days_in_grade,
                detail: sprintf('%d of %d days', $daysInGrade, $requirement->min_days_in_grade),
            ) : null,
            filled($requirement->min_days_in_service) ? new RequirementResultData(
                label: 'Time in service',
                met: $daysInService >= $requirement->min_days_in_service,
                detail: sprintf('%d of %d days', $daysInService, $requirement->min_days_in_service),
            ) : null,
            filled($requirement->min_events_attended_90d) ? new RequirementResultData(
                label: 'Events attended',
                met: $eventsAttended >= $requirement->min_events_attended_90d,
                detail: sprintf('%d of %d in the last %d days', $eventsAttended, $requirement->min_events_attended_90d, static::ATTENDANCE_WINDOW_DAYS),
            ) : null,
        ])
            ->merge(collect($requirement->required_qualification_ids ?? [])->map(fn (mixed $id): RequirementResultData => new RequirementResultData(
                label: 'Qualification',
                met: in_array((int) $id, $heldQualificationIds, true),
                detail: $qualificationNames->get((int) $id, 'Unknown qualification'),
            )))
            ->merge(collect($requirement->required_credential_ids ?? [])->map(fn (mixed $id): RequirementResultData => new RequirementResultData(
                label: 'Credential',
                met: in_array((int) $id, $heldCredentialIds, true),
                detail: $credentialNames->get((int) $id, 'Unknown credential'),
            )))
            ->filter()
            ->values()
            ->all();
    }
}
