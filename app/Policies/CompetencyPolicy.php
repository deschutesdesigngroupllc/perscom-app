<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Competency;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CompetencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_competency');
    }

    public function view(AuthUser $authUser, Competency $competency): bool
    {
        return $authUser->can('view_competency');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_competency');
    }

    public function update(AuthUser $authUser, Competency $competency): bool
    {
        return $authUser->can('update_competency');
    }

    public function delete(AuthUser $authUser, Competency $competency): bool
    {
        return $authUser->can('delete_competency');
    }

    public function restore(AuthUser $authUser, Competency $competency): bool
    {
        return $authUser->can('restore_competency');
    }

    public function forceDelete(AuthUser $authUser, Competency $competency): bool
    {
        return $authUser->can('force_delete_competency');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_competency');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_competency');
    }

    public function replicate(AuthUser $authUser, Competency $competency): bool
    {
        return $authUser->can('replicate_competency');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_competency');
    }
}
