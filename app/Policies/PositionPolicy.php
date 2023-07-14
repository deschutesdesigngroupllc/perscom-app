<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class PositionPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:position') || $user->tokenCan('view:position');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'view:position') || $user->tokenCan('view:position');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:position') || $user->tokenCan('create:position');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'update:position') || $user->tokenCan('update:position');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user->tokenCan('delete:position');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user->tokenCan('delete:position');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user->tokenCan('delete:position');
    }
}
