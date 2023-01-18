<?php

namespace App\Policies;

use App\Models\Award;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class AwardPolicy
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
        return $user->hasPermissionTo('view:award') || $user->tokenCan('view:award');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Award $award)
    {
        return $user->hasPermissionTo('view:award') || $user->tokenCan('view:award');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:award') || $user->tokenCan('create:award');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Award $award)
    {
        return $user->hasPermissionTo('update:award') || $user->tokenCan('update:award');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Award $award)
    {
        return $user->hasPermissionTo('delete:award') || $user->tokenCan('delete:award');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Award $award)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Award $award)
    {
        //
    }
}
