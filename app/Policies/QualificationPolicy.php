<?php

namespace App\Policies;

use App\Models\Qualification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class QualificationPolicy
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
        return $user->hasPermissionTo('view:qualification') || $user->tokenCan('view:qualification');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Qualification  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Qualification $qualification)
    {
        return $user->hasPermissionTo('view:qualification') || $user->tokenCan('view:qualification');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:qualification') || $user->tokenCan('create:qualification');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Qualification  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Qualification $qualification)
    {
        return $user->hasPermissionTo('update:qualification') || $user->tokenCan('update:qualification');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Qualification  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Qualification $qualification)
    {
        return $user->hasPermissionTo('delete:qualification') || $user->tokenCan('delete:qualification');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Qualification  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Qualification $qualification)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Qualification  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Qualification $qualification)
    {
        //
    }
}
