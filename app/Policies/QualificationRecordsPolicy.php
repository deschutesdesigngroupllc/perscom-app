<?php

namespace App\Policies;

use App\Models\QualificationRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class QualificationRecordsPolicy extends Policy
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
    public function view(User $user = null, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'view:qualificationrecord') ||
               $qualification->user?->id === $user?->id ||
               $user?->tokenCan('view:qualificationrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:qualificationrecord') ||
               $user?->tokenCan('create:qualificationrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'update:qualificationrecord') ||
               $user?->tokenCan('update:qualificationrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               $user?->tokenCan('delete:qualificationrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               $user?->tokenCan('delete:qualificationrecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               $user?->tokenCan('delete:qualificationrecord');
    }
}
