<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TrainingRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TrainingRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_training_record');
    }

    public function view(AuthUser $authUser, TrainingRecord $trainingRecord): bool
    {
        return $authUser->can('view_training_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_training_record');
    }

    public function update(AuthUser $authUser, TrainingRecord $trainingRecord): bool
    {
        return $authUser->can('update_training_record');
    }

    public function delete(AuthUser $authUser, TrainingRecord $trainingRecord): bool
    {
        return $authUser->can('delete_training_record');
    }

    public function restore(AuthUser $authUser, TrainingRecord $trainingRecord): bool
    {
        return $authUser->can('restore_training_record');
    }

    public function forceDelete(AuthUser $authUser, TrainingRecord $trainingRecord): bool
    {
        return $authUser->can('force_delete_training_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_training_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_training_record');
    }

    public function replicate(AuthUser $authUser, TrainingRecord $trainingRecord): bool
    {
        return $authUser->can('replicate_training_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_training_record');
    }
}
