<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Specialty;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SpecialtyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_specialty');
    }

    public function view(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('view_specialty');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_specialty');
    }

    public function update(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('update_specialty');
    }

    public function delete(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('delete_specialty');
    }

    public function restore(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('restore_specialty');
    }

    public function forceDelete(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('force_delete_specialty');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_specialty');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_specialty');
    }

    public function replicate(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('replicate_specialty');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_specialty');
    }
}
