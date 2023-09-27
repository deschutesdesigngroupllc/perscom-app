<?php

namespace App\Policies;

use App\Models\Award;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AwardPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:award') || $user?->tokenCan('view:award');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Award $award)
    {
        return $this->hasPermissionTo($user, 'view:award') || $user?->tokenCan('view:award');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:award') || $user?->tokenCan('create:award');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Award $award)
    {
        return $this->hasPermissionTo($user, 'update:award') || $user?->tokenCan('update:award');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Award $award)
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user?->tokenCan('delete:award');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Award $award)
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user?->tokenCan('delete:award');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Award $award)
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user?->tokenCan('delete:award');
    }
}
