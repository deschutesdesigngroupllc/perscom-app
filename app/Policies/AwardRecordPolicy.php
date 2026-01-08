<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AwardRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AwardRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_award_record');
    }

    public function view(AuthUser $authUser, AwardRecord $awardRecord): bool
    {
        return $authUser->can('view_award_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_award_record');
    }

    public function update(AuthUser $authUser, AwardRecord $awardRecord): bool
    {
        return $authUser->can('update_award_record');
    }

    public function delete(AuthUser $authUser, AwardRecord $awardRecord): bool
    {
        return $authUser->can('delete_award_record');
    }

    public function restore(AuthUser $authUser, AwardRecord $awardRecord): bool
    {
        return $authUser->can('restore_award_record');
    }

    public function forceDelete(AuthUser $authUser, AwardRecord $awardRecord): bool
    {
        return $authUser->can('force_delete_award_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_award_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_award_record');
    }

    public function replicate(AuthUser $authUser, AwardRecord $awardRecord): bool
    {
        return $authUser->can('replicate_award_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_award_record');
    }
}
