<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class StatusPolicy extends Policy
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
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'view:status') || $user->tokenCan('view:status');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'view:status') || $user->tokenCan('view:status');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:status') || $user->tokenCan('create:status');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'update:status') || $user->tokenCan('update:status');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user->tokenCan('delete:status');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user->tokenCan('delete:status');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user->tokenCan('delete:status');
    }
}
