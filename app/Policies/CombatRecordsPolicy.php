<?php

namespace App\Policies;

use App\Models\CombatRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class CombatRecordsPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CombatRecord $combat)
    {
        return $this->hasPermissionTo($user, 'view:combatrecord') ||
               $combat->user?->id === $user->id ||
               $user->tokenCan('view:combatrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:combatrecord') || $user->tokenCan('create:combatrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CombatRecord $combat)
    {
        return $this->hasPermissionTo($user, 'update:combatrecord') || $user->tokenCan('update:combatrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CombatRecord $combat)
    {
        return $this->hasPermissionTo($user, 'delete:combatrecord') || $user->tokenCan('delete:combatrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CombatRecord $combat)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CombatRecord $combat)
    {
        //
    }
}
