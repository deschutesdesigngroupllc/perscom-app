<?php

namespace App\Policies;

use App\Models\AssignmentRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AssignmentRecordsPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'view:assignmentrecord') ||
               $assignment->user?->id === $user?->id ||
               $user?->tokenCan('view:assignmentrecord');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:assignmentrecord') || $user?->tokenCan('create:assignmentrecord');
    }

    public function update(User $user = null, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'update:assignmentrecord') || $user?->tokenCan('update:assignmentrecord');
    }

    public function delete(User $user = null, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || $user?->tokenCan('delete:assignmentrecord');
    }

    public function restore(User $user = null, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || $user?->tokenCan('delete:assignmentrecord');
    }

    public function forceDelete(User $user = null, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || $user?->tokenCan('delete:assignmentrecord');
    }
}
