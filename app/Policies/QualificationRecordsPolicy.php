<?php

namespace App\Policies;

use App\Models\QualificationRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class QualificationRecordsPolicy extends Policy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualificationRecord  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'view:qualificationrecord') ||
               $qualification->user?->id === $user->id ||
               $user->tokenCan('view:qualificationrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:qualificationrecord') ||
               $user->tokenCan('create:qualificationrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualificationRecord  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'update:qualificationrecord') ||
               $user->tokenCan('update:qualificationrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualificationRecord  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, QualificationRecord $qualification)
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               $user->tokenCan('delete:qualificationrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualificationRecord  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, QualificationRecord $qualification)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualificationRecord  $qualification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, QualificationRecord $qualification)
    {
        //
    }
}
