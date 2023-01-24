<?php

namespace App\Policies;

use App\Models\AssignmentRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class AssignmentRecordsPolicy
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
        return $user->hasPermissionTo('view:assignmentrecord', 'web') || $user->tokenCan('view:assignmentrecord');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AssignmentRecord $assignment)
    {
        return $user->hasPermissionTo('view:assignmentrecord', 'web') ||
               $assignment->user?->id === $user->id ||
               $user->tokenCan('view:assignmentrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:assignmentrecord', 'web') || $user->tokenCan('create:assignmentrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AssignmentRecord $assignment)
    {
        return $user->hasPermissionTo('update:assignmentrecord', 'web') || $user->tokenCan('update:assignmentrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AssignmentRecord $assignment)
    {
        return $user->hasPermissionTo('delete:assignmentrecord', 'web') || $user->tokenCan('delete:assignmentrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AssignmentRecord $assignment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AssignmentRecord $assignment)
    {
        //
    }
}
