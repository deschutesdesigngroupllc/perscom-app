<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class SpecialtyPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:specialty') || $user?->tokenCan('view:specialty');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'view:specialty') || $user?->tokenCan('view:specialty');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:specialty') || $user?->tokenCan('create:specialty');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'update:specialty') || $user?->tokenCan('update:specialty');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'delete:specialty') || $user?->tokenCan('delete:specialty');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'delete:specialty') || $user?->tokenCan('delete:specialty');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'delete:specialty') || $user?->tokenCan('delete:specialty');
    }
}
