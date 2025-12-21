<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CombatRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CombatRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_combat_record');
    }

    public function view(AuthUser $authUser, CombatRecord $combatRecord): bool
    {
        return $authUser->can('view_combat_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_combat_record');
    }

    public function update(AuthUser $authUser, CombatRecord $combatRecord): bool
    {
        return $authUser->can('update_combat_record');
    }

    public function delete(AuthUser $authUser, CombatRecord $combatRecord): bool
    {
        return $authUser->can('delete_combat_record');
    }

    public function restore(AuthUser $authUser, CombatRecord $combatRecord): bool
    {
        return $authUser->can('restore_combat_record');
    }

    public function forceDelete(AuthUser $authUser, CombatRecord $combatRecord): bool
    {
        return $authUser->can('force_delete_combat_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_combat_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_combat_record');
    }

    public function replicate(AuthUser $authUser, CombatRecord $combatRecord): bool
    {
        return $authUser->can('replicate_combat_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_combat_record');
    }
}
