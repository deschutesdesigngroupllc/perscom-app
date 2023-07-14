<?php

namespace App\Policies;

use App\Models\Award;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class AwardPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:award') || $user->tokenCan('view:award');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'view:award') || $user->tokenCan('view:award');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:award') || $user->tokenCan('create:award');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'update:award') || $user->tokenCan('update:award');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user->tokenCan('delete:award');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user->tokenCan('delete:award');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user->tokenCan('delete:award');
    }
}
