<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Award;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AwardPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_award');
    }

    public function view(AuthUser $authUser, Award $award): bool
    {
        return $authUser->can('view_award');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_award');
    }

    public function update(AuthUser $authUser, Award $award): bool
    {
        return $authUser->can('update_award');
    }

    public function delete(AuthUser $authUser, Award $award): bool
    {
        return $authUser->can('delete_award');
    }

    public function restore(AuthUser $authUser, Award $award): bool
    {
        return $authUser->can('restore_award');
    }

    public function forceDelete(AuthUser $authUser, Award $award): bool
    {
        return $authUser->can('force_delete_award');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_award');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_award');
    }

    public function replicate(AuthUser $authUser, Award $award): bool
    {
        return $authUser->can('replicate_award');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_award');
    }
}
