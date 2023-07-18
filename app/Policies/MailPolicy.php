<?php

namespace App\Policies;

use App\Models\Mail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class MailPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return true|void
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $this->hasPermissionTo($user, 'view:mail') || $user->tokenCan('view:mail');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Mail $mail)
    {
        return $this->hasPermissionTo($user, 'view:mail') || $user->tokenCan('view:mail');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:mail') || $user->tokenCan('create:mail');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Mail $mail)
    {
        return $this->hasPermissionTo($user, 'update:mail') || $user->tokenCan('update:mail');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Mail $mail)
    {
        return $this->hasPermissionTo($user, 'delete:mail') || $user->tokenCan('delete:mail');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Mail $mail)
    {
        return $this->hasPermissionTo($user, 'delete:mail') || $user->tokenCan('delete:mail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Mail $mail)
    {
        return $this->hasPermissionTo($user, 'delete:mail') || $user->tokenCan('delete:mail');
    }
}
