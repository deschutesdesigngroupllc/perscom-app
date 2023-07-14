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
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'view:qualification') || $user->tokenCan('view:qualification');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'view:qualification') || $user->tokenCan('view:qualification');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:qualification') || $user->tokenCan('create:qualification');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'update:qualification') || $user->tokenCan('update:qualification');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || $user->tokenCan('delete:qualification');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || $user->tokenCan('delete:qualification');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || $user->tokenCan('delete:qualification');
    }
}
