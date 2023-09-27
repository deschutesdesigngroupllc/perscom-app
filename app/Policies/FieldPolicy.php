<?php

namespace App\Policies;

use App\Models\Field;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class FieldPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:field') || $user?->tokenCan('view:field');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Field $field)
    {
        return $this->hasPermissionTo($user, 'view:field') || $user?->tokenCan('view:field');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:field') || $user?->tokenCan('create:field');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Field $field)
    {
        return $this->hasPermissionTo($user, 'update:field') || $user?->tokenCan('update:field');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Field $field)
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user?->tokenCan('delete:field');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Field $field)
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user?->tokenCan('delete:field');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Field $field)
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user?->tokenCan('delete:field');
    }
}
