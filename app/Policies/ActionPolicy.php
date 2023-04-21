<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use Laravel\Nova\Actions\ActionEvent;

class ActionPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:log') || $user->tokenCan('view:log');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ActionEvent $actionEvent)
    {
        return $this->hasPermissionTo($user, 'view:log') || $user->tokenCan('view:log');
    }
}
