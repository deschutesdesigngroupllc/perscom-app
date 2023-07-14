<?php

namespace App\Policies;

use App\Models\AwardRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class AwardRecordsPolicy extends Policy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'view:awardrecord') ||
               $award->user?->id === $user->id ||
               $user->tokenCan('view:awardrecord');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:awardrecord') || $user->tokenCan('create:awardrecord');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'update:awardrecord') || $user->tokenCan('update:awardrecord');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user->tokenCan('delete:awardrecord');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user->tokenCan('delete:awardrecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user->tokenCan('delete:awardrecord');
    }
}
