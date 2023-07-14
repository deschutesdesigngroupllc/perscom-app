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
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'view:rank') || $user->tokenCan('view:rank');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'view:rank') || $user->tokenCan('view:rank');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:rank') || $user->tokenCan('create:rank');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'update:rank') || $user->tokenCan('update:rank');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user->tokenCan('delete:rank');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user->tokenCan('delete:rank');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user->tokenCan('delete:rank');
    }
}
