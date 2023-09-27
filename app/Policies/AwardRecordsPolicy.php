<?php

namespace App\Policies;

use App\Models\AwardRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AwardRecordsPolicy extends Policy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, AwardRecord $award)
    {
        return $this->hasPermissionTo($user, 'view:awardrecord') ||
               $award->user?->id === $user?->id ||
               $user?->tokenCan('view:awardrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:awardrecord') || $user?->tokenCan('create:awardrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, AwardRecord $award)
    {
        return $this->hasPermissionTo($user, 'update:awardrecord') || $user?->tokenCan('update:awardrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, AwardRecord $award)
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user?->tokenCan('delete:awardrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, AwardRecord $award)
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user?->tokenCan('delete:awardrecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, AwardRecord $award)
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user?->tokenCan('delete:awardrecord');
    }
}
