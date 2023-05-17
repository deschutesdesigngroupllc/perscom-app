<?php

namespace App\Policies;

use App\Models\Qualification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class QualificationPolicy extends Policy
{
    use HandlesAuthorization;

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
    public function viewAny(User $user)
    {
        return $this->hasPermissionTo($user, 'view:qualification') || $user->tokenCan('view:qualification');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Qualification $qualification)
    {
        return $this->hasPermissionTo($user, 'view:qualification') || $user->tokenCan('view:qualification');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:qualification') || $user->tokenCan('create:qualification');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Qualification $qualification)
    {
        return $this->hasPermissionTo($user, 'update:qualification') || $user->tokenCan('update:qualification');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Qualification $qualification)
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || $user->tokenCan('delete:qualification');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Qualification $qualification)
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || $user->tokenCan('delete:qualification');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Qualification $qualification)
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || $user->tokenCan('delete:qualification');
    }
}
