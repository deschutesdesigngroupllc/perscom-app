<?php

namespace App\Policies;

use App\Models\Field;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class FieldPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:field') || $user->tokenCan('view:field');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'view:field') || $user->tokenCan('view:field');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:field') || $user->tokenCan('create:field');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'update:field') || $user->tokenCan('update:field');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user->tokenCan('delete:field');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user->tokenCan('delete:field');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user->tokenCan('delete:field');
    }
}
