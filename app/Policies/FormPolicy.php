<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class FormPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:form') || $user?->tokenCan('view:form');
    }

    /**x
     * Determine whether the user can view the model.
     *
     * @param \App\Models\?User $user = null
     * @param \App\Models\Form $form
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Form $form)
    {
        return $this->hasPermissionTo($user, 'view:form') || $user?->tokenCan('view:form');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:form') || $user?->tokenCan('create:form');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Form $form)
    {
        return $this->hasPermissionTo($user, 'update:form') || $user?->tokenCan('update:form');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Form $form)
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user?->tokenCan('delete:form');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Form $form)
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user?->tokenCan('delete:form');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Form $form)
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user?->tokenCan('delete:form');
    }
}
