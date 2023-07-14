<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class UserPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:user') || $user->tokenCan('view:user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'view:user') || $user->id === $model->id || $user->tokenCan('view:user');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:user') || $user->tokenCan('create:user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'update:user') ||
               $user->id === $model->id ||
               $user->tokenCan('update:user');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || $user->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || $user->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || $user->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can impersonate another model.
     */
    public function impersonate(User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'impersonate:user') || $user->tokenCan('impersonate:user');
    }

    /**
     * Determine whether the user can add a note.
     */
    public function note(User $user): bool
    {
        return $this->hasPermissionTo($user, 'note:user') || $user->tokenCan('note:user');
    }

    public function billing(User $user): bool
    {
        return $this->hasPermissionTo($user, 'manage:billing') || $user->tokenCan('manage:billing');
    }

    public function api(User $user): bool
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    public function webhook(User $user): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }
}
