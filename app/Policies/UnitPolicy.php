<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class UnitPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:unit') || $user->tokenCan('view:unit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'view:unit') || $user->tokenCan('view:unit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:unit') || $user->tokenCan('create:user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'update:unit') || $user->tokenCan('update:user');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user->tokenCan('delete:user');
    }
}
