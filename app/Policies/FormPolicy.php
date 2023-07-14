<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class FormPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:form') || $user->tokenCan('view:form');
    }

    /**x
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Form $form
     */
    public function view(User $user, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'view:form') || $user->tokenCan('view:form');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:form') || $user->tokenCan('create:form');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'update:form') || $user->tokenCan('update:form');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user->tokenCan('delete:form');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user->tokenCan('delete:form');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user->tokenCan('delete:form');
    }
}
