<?php

namespace App\Policies;

use App\Models\AssignmentRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class AssignmentRecordsPolicy extends Policy
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
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AssignmentRecord $assignment)
    {
        return $this->hasPermissionTo($user, 'view:assignmentrecord') ||
               $assignment->user?->id === $user->id ||
               $user->tokenCan('view:assignmentrecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:assignmentrecord') || $user->tokenCan('create:assignmentrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AssignmentRecord $assignment)
    {
        return $this->hasPermissionTo($user, 'update:assignmentrecord') || $user->tokenCan('update:assignmentrecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AssignmentRecord $assignment)
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || $user->tokenCan('delete:assignmentrecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AssignmentRecord $assignment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AssignmentRecord $assignment)
    {
        //
    }
}
