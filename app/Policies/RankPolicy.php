<?php

namespace App\Policies;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class RankPolicy
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
        return $user->hasPermissionTo('view:rank');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rank  $rank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Rank $rank)
    {
        return $user->hasPermissionTo('view:rank');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:rank');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rank  $rank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Rank $rank)
    {
        return $user->hasPermissionTo('update:rank');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rank  $rank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Rank $rank)
    {
        return $user->hasPermissionTo('delete:rank');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rank  $rank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Rank $rank)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rank  $rank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Rank $rank)
    {
        //
    }
}
