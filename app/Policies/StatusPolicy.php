<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class StatusPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:status') || $user?->tokenCan('view:status');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Status $status)
    {
        return $this->hasPermissionTo($user, 'view:status') || $user?->tokenCan('view:status');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:status') || $user?->tokenCan('create:status');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Status $status)
    {
        return $this->hasPermissionTo($user, 'update:status') || $user?->tokenCan('update:status');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Status $status)
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user?->tokenCan('delete:status');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Status $status)
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user?->tokenCan('delete:status');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Status $status)
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user?->tokenCan('delete:status');
    }
}
