<?php

namespace App\Policies;

use App\Models\Records\Combat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class CombatRecordsPolicy
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view:combatrecord') || $user->tokenCan('view:combatrecord');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Records\Combat  $combat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Combat $combat)
    {
        return $user->hasPermissionTo('view:combatrecord') || $combat->user?->id === $user->id || $user->tokenCan('view:combatrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:combatrecord') || $user->tokenCan('create:combatrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Records\Combat  $combat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Combat $combat)
    {
        return $user->hasPermissionTo('update:combatrecord') || $user->tokenCan('update:combatrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Records\Combat  $combat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Combat $combat)
    {
        return $user->hasPermissionTo('delete:combatrecord') || $user->tokenCan('delete:combatrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Records\Combat  $combat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Combat $combat)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Records\Combat  $combat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Combat $combat)
    {
        //
    }
}
