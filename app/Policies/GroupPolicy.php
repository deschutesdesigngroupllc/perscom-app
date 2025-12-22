<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Group;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_group');
    }

    public function view(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('view_group');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_group');
    }

    public function update(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('update_group');
    }

    public function delete(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('delete_group');
    }

    public function restore(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('restore_group');
    }

    public function forceDelete(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('force_delete_group');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_group');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_group');
    }

    public function replicate(AuthUser $authUser, Group $group): bool
    {
        return $authUser->can('replicate_group');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_group');
    }
}
