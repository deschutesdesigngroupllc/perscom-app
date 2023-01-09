<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class SpecialtyPolicy
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
        return $user->hasPermissionTo('view:specialty');
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
        return $user->hasPermissionTo('view:specialty');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:specialty');
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
        return $user->hasPermissionTo('update:specialty');
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
        return $user->hasPermissionTo('delete:specialty');
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
