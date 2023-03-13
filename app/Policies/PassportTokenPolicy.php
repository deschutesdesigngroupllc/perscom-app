<?php
/*
 * Copyright (c) 2/11/23, 1:15 PM Deschutes Design Group LLC.year. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Policies;

use App\Features\ApiAccessFeature;
use App\Models\PassportToken;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class PassportTokenPolicy extends Policy
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

        if (Feature::inactive(ApiAccessFeature::class)) {
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
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassportToken  $token
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PassportToken $token)
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassportToken  $token
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PassportToken $token)
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassportToken  $token
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PassportToken $token)
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassportToken  $token
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PassportToken $token)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassportToken  $token
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PassportToken $token)
    {
        //
    }
}
