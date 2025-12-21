<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AssignmentRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AssignmentRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_assignment_record');
    }

    public function view(AuthUser $authUser, AssignmentRecord $assignmentRecord): bool
    {
        return $authUser->can('view_assignment_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_assignment_record');
    }

    public function update(AuthUser $authUser, AssignmentRecord $assignmentRecord): bool
    {
        return $authUser->can('update_assignment_record');
    }

    public function delete(AuthUser $authUser, AssignmentRecord $assignmentRecord): bool
    {
        return $authUser->can('delete_assignment_record');
    }

    public function restore(AuthUser $authUser, AssignmentRecord $assignmentRecord): bool
    {
        return $authUser->can('restore_assignment_record');
    }

    public function forceDelete(AuthUser $authUser, AssignmentRecord $assignmentRecord): bool
    {
        return $authUser->can('force_delete_assignment_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_assignment_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_assignment_record');
    }

    public function replicate(AuthUser $authUser, AssignmentRecord $assignmentRecord): bool
    {
        return $authUser->can('replicate_assignment_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_assignment_record');
    }
}
