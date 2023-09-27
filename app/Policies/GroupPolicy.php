<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class GroupPolicy extends Policy
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
     */
    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || $user?->tokenCan('view:group');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || $user?->tokenCan('view:group');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:group') || $user?->tokenCan('create:user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'update:group') || $user?->tokenCan('update:user');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || $user?->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || $user?->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || $user?->tokenCan('delete:user');
    }
}
