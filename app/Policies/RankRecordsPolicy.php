<?php

namespace App\Policies;

use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class RankRecordsPolicy extends Policy
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
    public function view(User $user = null, RankRecord $rank)
    {
        return $this->hasPermissionTo($user, 'view:rankrecord') ||
               $rank->user?->id === $user?->id ||
               $user?->tokenCan('view:rankrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:rankrecord') || $user?->tokenCan('create:rankrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, RankRecord $rank)
    {
        return $this->hasPermissionTo($user, 'update:rankrecord') || $user?->tokenCan('update:rankrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, RankRecord $rank)
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || $user?->tokenCan('delete:rankrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, RankRecord $rank)
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || $user?->tokenCan('delete:rankrecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, RankRecord $rank)
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || $user?->tokenCan('delete:rankrecord');
    }
}
