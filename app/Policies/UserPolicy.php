<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:user') || $user->tokenCan('view:user');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        return $this->hasPermissionTo($user, 'view:user') || $user->id === $model->id || $user->tokenCan('view:user');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:user') || $user->tokenCan('create:user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        return $this->hasPermissionTo($user, 'update:user') ||
               $user->id === $model->id ||
               $user->tokenCan('update:user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        return $this->hasPermissionTo($user, 'delete:user') || $user->tokenCan('delete:user');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can impersonate another model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function impersonate(User $user, User $model)
    {
        return $this->hasPermissionTo($user, 'impersonate:user') || $user->tokenCan('impersonate:user');
    }

    /**
     * Determine whether the user can add a note.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function note(User $user)
    {
        return $this->hasPermissionTo($user, 'note:user') || $user->tokenCan('note:user');
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function widget(User $user)
    {
        return $this->hasPermissionTo($user, 'access:widget') || $user->tokenCan('access:widget') || Auth::guard('jwt')->check();
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function billing(User $user)
    {
        return $this->hasPermissionTo($user, 'manage:billing') || $user->tokenCan('manage:billing');
    }
}
