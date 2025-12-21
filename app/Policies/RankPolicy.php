<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Rank;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RankPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_rank');
    }

    public function view(AuthUser $authUser, Rank $rank): bool
    {
        return $authUser->can('view_rank');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_rank');
    }

    public function update(AuthUser $authUser, Rank $rank): bool
    {
        return $authUser->can('update_rank');
    }

    public function delete(AuthUser $authUser, Rank $rank): bool
    {
        return $authUser->can('delete_rank');
    }

    public function restore(AuthUser $authUser, Rank $rank): bool
    {
        return $authUser->can('restore_rank');
    }

    public function forceDelete(AuthUser $authUser, Rank $rank): bool
    {
        return $authUser->can('force_delete_rank');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_rank');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_rank');
    }

    public function replicate(AuthUser $authUser, Rank $rank): bool
    {
        return $authUser->can('replicate_rank');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_rank');
    }
}
