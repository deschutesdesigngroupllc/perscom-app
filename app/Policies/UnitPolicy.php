<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class UnitPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:unit') || $user?->tokenCan('view:unit');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Unit $unit)
    {
        return $this->hasPermissionTo($user, 'view:unit') || $user?->tokenCan('view:unit');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:unit') || $user?->tokenCan('create:user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Unit $unit)
    {
        return $this->hasPermissionTo($user, 'update:unit') || $user?->tokenCan('update:user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Unit $unit)
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user?->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Unit $unit)
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user?->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Unit $unit)
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user?->tokenCan('delete:user');
    }
}
