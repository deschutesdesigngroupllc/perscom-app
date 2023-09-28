<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class PositionPolicy extends Policy
{
    /**
     * @return false|void
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
    public function viewAny(User $user = null)
    {
        return $this->hasPermissionTo($user, 'view:position') || $user?->tokenCan('view:position');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Position $position)
    {
        return $this->hasPermissionTo($user, 'view:position') || $user?->tokenCan('view:position');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:position') || $user?->tokenCan('create:position');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Position $position)
    {
        return $this->hasPermissionTo($user, 'update:position') || $user?->tokenCan('update:position');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Position $position)
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user?->tokenCan('delete:position');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Position $position)
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user?->tokenCan('delete:position');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Position $position)
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user?->tokenCan('delete:position');
    }
}
