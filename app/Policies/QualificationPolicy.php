<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Qualification;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class QualificationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_qualification');
    }

    public function view(AuthUser $authUser, Qualification $qualification): bool
    {
        return $authUser->can('view_qualification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_qualification');
    }

    public function update(AuthUser $authUser, Qualification $qualification): bool
    {
        return $authUser->can('update_qualification');
    }

    public function delete(AuthUser $authUser, Qualification $qualification): bool
    {
        return $authUser->can('delete_qualification');
    }

    public function restore(AuthUser $authUser, Qualification $qualification): bool
    {
        return $authUser->can('restore_qualification');
    }

    public function forceDelete(AuthUser $authUser, Qualification $qualification): bool
    {
        return $authUser->can('force_delete_qualification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_qualification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_qualification');
    }

    public function replicate(AuthUser $authUser, Qualification $qualification): bool
    {
        return $authUser->can('replicate_qualification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_qualification');
    }
}
