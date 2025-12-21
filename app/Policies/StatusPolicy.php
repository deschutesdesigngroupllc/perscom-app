<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Status;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_status');
    }

    public function view(AuthUser $authUser, Status $status): bool
    {
        return $authUser->can('view_status');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_status');
    }

    public function update(AuthUser $authUser, Status $status): bool
    {
        return $authUser->can('update_status');
    }

    public function delete(AuthUser $authUser, Status $status): bool
    {
        return $authUser->can('delete_status');
    }

    public function restore(AuthUser $authUser, Status $status): bool
    {
        return $authUser->can('restore_status');
    }

    public function forceDelete(AuthUser $authUser, Status $status): bool
    {
        return $authUser->can('force_delete_status');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_status');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_status');
    }

    public function replicate(AuthUser $authUser, Status $status): bool
    {
        return $authUser->can('replicate_status');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_status');
    }
}
