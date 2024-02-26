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

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'view:assignmentrecord')
            || $assignment->user?->id === optional($user)->id
            || optional($user)->tokenCan('view:assignmentrecord');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:assignmentrecord') || optional($user)->tokenCan('create:assignmentrecord');
    }

    public function update(?User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'update:assignmentrecord') || optional($user)->tokenCan('update:assignmentrecord');
    }

    public function delete(?User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || optional($user)->tokenCan('delete:assignmentrecord');
    }

    public function restore(?User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || optional($user)->tokenCan('delete:assignmentrecord');
    }

    public function forceDelete(?User $user, AssignmentRecord $assignment): bool
    {
        return $this->hasPermissionTo($user, 'delete:assignmentrecord') || optional($user)->tokenCan('delete:assignmentrecord');
    }
}
