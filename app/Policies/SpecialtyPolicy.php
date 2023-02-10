<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class SpecialtyPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:specialty') || $user->tokenCan('view:specialty');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specialty  $mos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'view:specialty') || $user->tokenCan('view:specialty');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:specialty') || $user->tokenCan('create:specialty');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specialty  $mos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'update:specialty') || $user->tokenCan('update:specialty');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specialty  $mos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Specialty $mos)
    {
        return $this->hasPermissionTo($user, 'delete:specialty') || $user->tokenCan('delete:specialty');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specialty  $mos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Specialty $mos)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specialty  $mos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Specialty $mos)
    {
        //
    }
}
