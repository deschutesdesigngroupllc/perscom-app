<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class FormPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return bool
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return Gate::check('create', Submission::class);
    }

    /**x
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Form $form
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Form $form)
    {
        return $this->hasPermissionTo($user, 'view:form') || $user->tokenCan('view:form');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:form') || $user->tokenCan('create:form');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Form $form)
    {
        return $this->hasPermissionTo($user, 'update:form') || $user->tokenCan('update:form');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Form $form)
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user->tokenCan('delete:form');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Form $form)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Form $form)
    {
        //
    }
}
