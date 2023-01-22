<?php

namespace App\Policies\Passport;

use App\Models\Passport\Log;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class LogPolicy
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

        if (! tenant()->canAccessApi()) {
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
        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Passport\Log  $log
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Log $log)
    {
        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Passport\Log  $log
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Log $log)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Passport\Log  $log
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Log $log)
    {
        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Passport\Log  $log
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Log $log)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Passport\Log  $log
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Log $log)
    {
        //
    }
}
