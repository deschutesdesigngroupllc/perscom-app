<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RankRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RankRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_rank_record');
    }

    public function view(AuthUser $authUser, RankRecord $rankRecord): bool
    {
        return $authUser->can('view_rank_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_rank_record');
    }

    public function update(AuthUser $authUser, RankRecord $rankRecord): bool
    {
        return $authUser->can('update_rank_record');
    }

    public function delete(AuthUser $authUser, RankRecord $rankRecord): bool
    {
        return $authUser->can('delete_rank_record');
    }

    public function restore(AuthUser $authUser, RankRecord $rankRecord): bool
    {
        return $authUser->can('restore_rank_record');
    }

    public function forceDelete(AuthUser $authUser, RankRecord $rankRecord): bool
    {
        return $authUser->can('force_delete_rank_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_rank_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_rank_record');
    }

    public function replicate(AuthUser $authUser, RankRecord $rankRecord): bool
    {
        return $authUser->can('replicate_rank_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_rank_record');
    }
}
