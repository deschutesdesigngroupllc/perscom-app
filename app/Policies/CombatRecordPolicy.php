<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CombatRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CombatRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_combatrecord');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CombatRecord $combatRecord): bool
    {
        return $user->can('view_combatrecord');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_combatrecord');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CombatRecord $combatRecord): bool
    {
        return $user->can('update_combatrecord');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CombatRecord $combatRecord): bool
    {
        return $user->can('delete_combatrecord');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_combatrecord');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CombatRecord $combatRecord): bool
    {
        return $user->can('force_delete_combatrecord');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_combatrecord');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CombatRecord $combatRecord): bool
    {
        return $user->can('restore_combatrecord');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_combatrecord');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CombatRecord $combatRecord): bool
    {
        return $user->can('replicate_combatrecord');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_combatrecord');
    }
}
