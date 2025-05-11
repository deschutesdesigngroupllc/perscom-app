<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AssignmentRecord;
use App\Models\Group;
use App\Models\Slot;
use App\Models\Unit;
use Illuminate\Support\Collection;

class RosterService
{
    /**
     * @param  Collection<int, Group>  $groups
     */
    public static function mergeSecondaryAssignmentRecordsForAutomaticRoster(Collection $groups): Collection
    {
        return $groups->map(fn (Group $group) => tap($group, fn (Group $group) => data_set($group, 'units', $group->units->map(fn (Unit $unit) => tap($unit, fn (Unit $unit) => data_set($unit, 'users', $unit->users->merge($unit->secondary_assignment_records->map(function (AssignmentRecord $record) {
            $user = $record->user;

            data_set($user, 'position', $record->position);
            data_set($user, 'specialty', $record->specialty);

            return $user;
        }))))))));
    }

    /**
     * @param  Collection<int, Group>  $groups
     */
    public static function mergeSecondaryAssignmentRecordsForManualRoster(Collection $groups): Collection
    {
        return $groups->map(fn (Group $group) => tap($group, fn (Group $group) => data_set($group, 'units', $group->units->map(fn (Unit $unit) => tap($unit, fn (Unit $unit) => data_set($unit, 'slots', $unit->slots->map(fn (Slot $slot) => tap($slot, fn (Slot $slot) => data_set($slot, 'users', $slot->users->merge($slot->secondary_assignment_records->map(function (AssignmentRecord $record) use ($slot) {
            $user = $record->user;

            data_set($user, 'position', $slot->position);
            data_set($user, 'specialty', $slot->specialty);

            return $user;
        })))))))))));
    }
}
