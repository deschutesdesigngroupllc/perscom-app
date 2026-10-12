<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Promotions\PromotionEvaluationData;
use App\Data\Roster\RosterMemberInsightData;
use App\Models\AssignmentRecord;
use App\Models\Enums\RosterMode;
use App\Models\Group;
use App\Models\Slot;
use App\Models\Unit;
use App\Models\User;
use App\Settings\DashboardSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RosterService
{
    /**
     * The relations every roster member needs to render a row.
     */
    public const array MEMBER_RELATIONS = ['position', 'specialty', 'unit', 'rank', 'rank.image', 'status'];

    /**
     * Load the roster's groups for the given mode with secondary assignments merged in
     * and every member's display relations loaded in a fixed number of queries.
     *
     * @return Collection<int, Group>
     */
    public static function groups(RosterMode $mode): Collection
    {
        $groups = $mode === RosterMode::MANUAL
            ? static::mergeSecondaryAssignmentRecordsForManualRoster(Group::query()->forManualRoster()->get())
            : static::mergeSecondaryAssignmentRecordsForAutomaticRoster(Group::query()->forAutomaticRoster()->get());

        new EloquentCollection(static::members($groups, $mode)->all())->loadMissing(static::MEMBER_RELATIONS);

        return $groups;
    }

    /**
     * Every member instance shown on the roster, including duplicates from secondary assignments.
     *
     * @param  Collection<int, Group>  $groups
     * @return Collection<int, User>
     */
    public static function members(Collection $groups, RosterMode $mode): Collection
    {
        return $groups
            ->flatMap(fn (Group $group): Collection => $group->units)
            ->flatMap(fn (Unit $unit): Collection => $mode === RosterMode::MANUAL
                ? $unit->slots->flatMap(fn (Slot $slot): Collection => Collection::wrap($slot->getAttribute('users')))
                : Collection::wrap($unit->getAttribute('users')))
            ->filter(fn (mixed $user): bool => $user instanceof User)
            ->values();
    }

    /**
     * Batch-calculate time in grade, readiness and promotion eligibility for roster members.
     *
     * @param  Collection<int, User>  $members
     * @return array<int, RosterMemberInsightData>
     */
    public static function insights(Collection $members): array
    {
        $users = new EloquentCollection($members->unique(fn (User $user): int => (int) $user->getKey())->values()->all());
        $userIds = $users->modelKeys();

        if (blank($userIds)) {
            return [];
        }

        $lastRankChanges = DB::table('records_ranks')
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->selectRaw('user_id, MAX(created_at) as last_rank_change')
            ->pluck('last_rank_change', 'user_id');

        $expired = User::query()
            ->whereKey($userIds)
            ->where(fn (Builder $query): Builder => $query
                ->whereHasExpiredCredentials()
                ->orWhere(fn (Builder $query): Builder => $query->whereHasExpiredQualifications()))
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        $expiring = User::query()
            ->whereKey($userIds)
            ->where(fn (Builder $query): Builder => $query
                ->whereHasExpiringCredentials()
                ->orWhere(fn (Builder $query): Builder => $query->whereHasExpiringQualifications()))
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        $evaluations = PromotionEligibilityService::evaluateMany($users);

        return $users->mapWithKeys(function (User $user) use ($lastRankChanges, $expired, $expiring, $evaluations): array {
            $lastRankChange = $lastRankChanges->get($user->getKey());
            $inGradeSince = filled($lastRankChange) ? Date::parse($lastRankChange) : $user->created_at;

            /** @var PromotionEvaluationData|null $evaluation */
            $evaluation = $evaluations->get($user->getKey());

            return [(int) $user->getKey() => new RosterMemberInsightData(
                daysInGrade: $inGradeSince ? (int) $inGradeSince->diffInDays(now()) : null,
                hasExpired: in_array((int) $user->getKey(), $expired, true),
                hasExpiring: in_array((int) $user->getKey(), $expiring, true),
                promotionEligible: $evaluation?->eligible ?? false,
                nextRank: $evaluation?->eligible ? $evaluation->nextRank?->name : null,
            )];
        })->all();
    }

    /**
     * @param  array{search?: ?string, rank_id?: mixed, status_id?: mixed, position_id?: mixed, unit_id?: mixed, online?: bool}  $filters
     */
    public static function isFiltering(array $filters): bool
    {
        return filled(trim((string) ($filters['search'] ?? '')))
            || filled($filters['rank_id'] ?? null)
            || filled($filters['status_id'] ?? null)
            || filled($filters['position_id'] ?? null)
            || (bool) ($filters['online'] ?? false);
    }

    /**
     * @param  array{search?: ?string, rank_id?: mixed, status_id?: mixed, position_id?: mixed, unit_id?: mixed, online?: bool}  $filters
     */
    public static function matches(User $user, array $filters): bool
    {
        $search = Str::lower(trim((string) ($filters['search'] ?? '')));

        if (filled($search)) {
            $position = $user->getAttribute('position');
            $specialty = $user->getAttribute('specialty');

            $haystack = Str::lower(collect([
                $user->display_name,
                $user->name,
                $user->rank?->name,
                $user->rank?->abbreviation,
                data_get($position, 'name'),
                data_get($specialty, 'name'),
                data_get($specialty, 'abbreviation'),
            ])->filter()->implode(' '));

            if (! Str::contains($haystack, $search)) {
                return false;
            }
        }

        if (filled($filters['rank_id'] ?? null) && (int) $user->rank_id !== (int) $filters['rank_id']) {
            return false;
        }

        if (filled($filters['status_id'] ?? null) && (int) $user->status_id !== (int) $filters['status_id']) {
            return false;
        }

        if (filled($filters['position_id'] ?? null) && (int) data_get($user->getAttribute('position'), 'id') !== (int) $filters['position_id']) {
            return false;
        }

        return ! ($filters['online'] ?? false) || $user->online;
    }

    /**
     * Build a group's nested unit tree, keeping only matching members when filters are applied.
     *
     * @param  array{search?: ?string, rank_id?: mixed, status_id?: mixed, position_id?: mixed, unit_id?: mixed, online?: bool}  $filters
     * @return array<int, array{unit: Unit, users: Collection<int, User>, slots: Collection<int, array{slot: Slot, users: Collection<int, User>}>, children: array<int, mixed>, total: int}>
     */
    public static function tree(Group $group, RosterMode $mode, array $filters = []): array
    {
        $units = $group->units;
        $unitIds = $units->modelKeys();
        $filtering = static::isFiltering($filters);

        $childrenByParent = $units->groupBy(fn (Unit $unit): int => in_array($unit->parent_id, $unitIds, false) && $unit->parent_id !== $unit->getKey()
            ? (int) $unit->parent_id
            : 0);

        $build = function (Unit $unit, array $visited) use (&$build, $childrenByParent, $mode, $filters, $filtering): ?array {
            $visited[] = (int) $unit->getKey();

            $children = Collection::wrap($childrenByParent->get((int) $unit->getKey(), []))
                ->reject(fn (Unit $child): bool => in_array((int) $child->getKey(), $visited, true))
                ->map(fn (Unit $child): ?array => $build($child, $visited))
                ->filter()
                ->values()
                ->all();

            $users = collect();
            $slots = collect();

            if ($mode === RosterMode::MANUAL) {
                $slots = $unit->slots
                    ->map(fn (Slot $slot): array => [
                        'slot' => $slot,
                        'users' => Collection::wrap($slot->getAttribute('users'))
                            ->filter(fn (User $user): bool => static::matches($user, $filters))
                            ->values(),
                    ])
                    ->when($filtering, fn (Collection $slots): Collection => $slots->filter(fn (array $slot): bool => $slot['users']->isNotEmpty()))
                    ->values();

                $count = $slots->sum(fn (array $slot): int => $slot['users']->count());
            } else {
                $users = Collection::wrap($unit->getAttribute('users'))
                    ->filter(fn (User $user): bool => static::matches($user, $filters))
                    ->values();

                $count = $users->count();
            }

            $total = $count + collect($children)->sum('total');

            if ($filtering && $total === 0) {
                return null;
            }

            return [
                'unit' => $unit,
                'users' => $users,
                'slots' => $slots,
                'children' => $children,
                'total' => $total,
            ];
        };

        $roots = filled($filters['unit_id'] ?? null)
            ? $units->filter(fn (Unit $unit): bool => (int) $unit->getKey() === (int) $filters['unit_id'])
            : Collection::wrap($childrenByParent->get(0, []));

        return $roots
            ->map(fn (Unit $unit): ?array => $build($unit, []))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Group>  $groups
     */
    public static function mergeSecondaryAssignmentRecordsForAutomaticRoster(Collection $groups): Collection
    {
        return $groups->map(fn (Group $group) => tap($group, function (Group $group): void {
            data_set($group, 'units', $group->units->map(fn (Unit $unit) => tap($unit, function (Unit $unit): void {
                data_set($unit, 'users', RosterService::sortUsers($unit->users->merge(
                    $unit->secondary_assignment_records->loadMissing(['position', 'specialty'])->map(function (AssignmentRecord $record) {
                        $user = $record->user;

                        data_set($user, 'position', $record->position);
                        data_set($user, 'specialty', $record->specialty);

                        return $user;
                    })
                )));
            })));
        }));
    }

    /**
     * @param  Collection<int, Group>  $groups
     */
    public static function mergeSecondaryAssignmentRecordsForManualRoster(Collection $groups): Collection
    {
        return $groups->map(fn (Group $group) => tap($group, function (Group $group): void {
            data_set($group, 'units', $group->units->map(fn (Unit $unit) => tap($unit, function (Unit $unit): void {
                data_set($unit, 'slots', $unit->slots->map(fn (Slot $slot) => tap($slot, function (Slot $slot): void {
                    data_set($slot, 'users', RosterService::sortUsers($slot->users->merge(
                        $slot->secondary_assignment_records->loadMissing(['position', 'specialty'])->map(function (AssignmentRecord $record) use ($slot) {
                            $user = $record->user;

                            data_set($user, 'position', $slot->position);
                            data_set($user, 'specialty', $slot->specialty);

                            return $user;
                        })
                    )));
                })));
            })));
        }));
    }

    /**
     * @param  Collection<int, User>  $users
     */
    public static function sortUsers(Collection $users): Collection
    {
        $settings = resolve(DashboardSettings::class);

        $order = Collection::wrap($settings->roster_sort_order)
            ->map(fn ($property) => Str::replaceMatches('/^[^.]+/', fn (array $matches) => Str::singular($matches[0]), $property))
            ->map(fn ($property): array => [$property, 'asc'])
            ->all();

        return $users->sortBy($order)->values();
    }
}
