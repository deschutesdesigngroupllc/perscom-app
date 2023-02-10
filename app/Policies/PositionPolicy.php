<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class PositionPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:position') || $user->tokenCan('view:position');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Position $position)
    {
        return $this->hasPermissionTo($user, 'view:position') || $user->tokenCan('view:position');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:position') || $user->tokenCan('create:position');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Position $position)
    {
        return $this->hasPermissionTo($user, 'update:position') || $user->tokenCan('update:position');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Position $position)
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user->tokenCan('delete:position');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Position $position)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Position $position)
    {
        //
    }
}
