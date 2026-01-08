<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\QualificationRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class QualificationRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_qualification_record');
    }

    public function view(AuthUser $authUser, QualificationRecord $qualificationRecord): bool
    {
        return $authUser->can('view_qualification_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_qualification_record');
    }

    public function update(AuthUser $authUser, QualificationRecord $qualificationRecord): bool
    {
        return $authUser->can('update_qualification_record');
    }

    public function delete(AuthUser $authUser, QualificationRecord $qualificationRecord): bool
    {
        return $authUser->can('delete_qualification_record');
    }

    public function restore(AuthUser $authUser, QualificationRecord $qualificationRecord): bool
    {
        return $authUser->can('restore_qualification_record');
    }

    public function forceDelete(AuthUser $authUser, QualificationRecord $qualificationRecord): bool
    {
        return $authUser->can('force_delete_qualification_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_qualification_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_qualification_record');
    }

    public function replicate(AuthUser $authUser, QualificationRecord $qualificationRecord): bool
    {
        return $authUser->can('replicate_qualification_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_qualification_record');
    }
}
