<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Submission;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SubmissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_submission');
    }

    public function view(AuthUser $authUser, Submission $submission): bool
    {
        return $authUser->can('view_submission');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_submission');
    }

    public function update(AuthUser $authUser, Submission $submission): bool
    {
        return $authUser->can('update_submission');
    }

    public function delete(AuthUser $authUser, Submission $submission): bool
    {
        return $authUser->can('delete_submission');
    }

    public function restore(AuthUser $authUser, Submission $submission): bool
    {
        return $authUser->can('restore_submission');
    }

    public function forceDelete(AuthUser $authUser, Submission $submission): bool
    {
        return $authUser->can('force_delete_submission');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_submission');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_submission');
    }

    public function replicate(AuthUser $authUser, Submission $submission): bool
    {
        return $authUser->can('replicate_submission');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_submission');
    }
}
