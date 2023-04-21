<?php

namespace App\Policies;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class RankPolicy extends Policy
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
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $this->hasPermissionTo($user, 'view:rank') || $user->tokenCan('view:rank');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Rank $rank)
    {
        return $this->hasPermissionTo($user, 'view:rank') || $user->tokenCan('view:rank');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:rank') || $user->tokenCan('create:rank');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Rank $rank)
    {
        return $this->hasPermissionTo($user, 'update:rank') || $user->tokenCan('update:rank');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Rank $rank)
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user->tokenCan('delete:rank');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Rank $rank)
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user->tokenCan('delete:rank');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Rank $rank)
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user->tokenCan('delete:rank');
    }
}
