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
    public function view(User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'view:assignmentrecord') ||
               $assignment->user?->id === $user->id ||
               $user->tokenCan('view:assignmentrecord');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:assignmentrecord') || $user->tokenCan('create:assignmentrecord');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'update:assignmentrecord') || $user->tokenCan('update:assignmentrecord');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || $user->tokenCan('delete:assignmentrecord');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || $user->tokenCan('delete:assignmentrecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || $user->tokenCan('delete:assignmentrecord');
    }
}
