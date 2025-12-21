<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Position;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PositionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_position');
    }

    public function view(AuthUser $authUser, Position $position): bool
    {
        return $authUser->can('view_position');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_position');
    }

    public function update(AuthUser $authUser, Position $position): bool
    {
        return $authUser->can('update_position');
    }

    public function delete(AuthUser $authUser, Position $position): bool
    {
        return $authUser->can('delete_position');
    }

    public function restore(AuthUser $authUser, Position $position): bool
    {
        return $authUser->can('restore_position');
    }

    public function forceDelete(AuthUser $authUser, Position $position): bool
    {
        return $authUser->can('force_delete_position');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_position');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_position');
    }

    public function replicate(AuthUser $authUser, Position $position): bool
    {
        return $authUser->can('replicate_position');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_position');
    }
}
