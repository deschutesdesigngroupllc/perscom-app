<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AssignmentRecord;
use App\Models\Group;
use App\Models\Slot;
use App\Models\Unit;
use App\Models\User;
use App\Settings\DashboardSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RosterService
{
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
        $settings = app(DashboardSettings::class);

        $order = Collection::wrap($settings->roster_sort_order)
            ->map(fn ($property) => Str::replaceMatches('/^[^.]+/', fn (array $matches) => Str::singular($matches[0]), $property))
            ->map(fn ($property): array => [$property, 'asc'])
            ->toArray();

        return $users->sortBy($order)->values();
    }
}
