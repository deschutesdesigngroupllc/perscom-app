<?php

namespace App\Policies;

use App\Models\AwardRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class AwardRecordsPolicy
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
        return $user->hasPermissionTo('view:awardrecord', 'web') || $user->tokenCan('view:awardrecord');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AwardRecord  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AwardRecord $award)
    {
        return $user->hasPermissionTo('view:awardrecord', 'web') ||
               $award->user?->id === $user->id ||
               $user->tokenCan('view:awardrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:awardrecord', 'web') || $user->tokenCan('create:awardrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AwardRecord  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AwardRecord $award)
    {
        return $user->hasPermissionTo('update:awardrecord', 'web') || $user->tokenCan('update:awardrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AwardRecord  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AwardRecord $award)
    {
        return $user->hasPermissionTo('delete:awardrecord', 'web') || $user->tokenCan('delete:awardrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AwardRecord  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AwardRecord $award)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AwardRecord  $award
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AwardRecord $award)
    {
        //
    }
}
