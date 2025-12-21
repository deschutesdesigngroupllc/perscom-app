<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Credential;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CredentialPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_credential');
    }

    public function view(AuthUser $authUser, Credential $credential): bool
    {
        return $authUser->can('view_credential');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_credential');
    }

    public function update(AuthUser $authUser, Credential $credential): bool
    {
        return $authUser->can('update_credential');
    }

    public function delete(AuthUser $authUser, Credential $credential): bool
    {
        return $authUser->can('delete_credential');
    }

    public function restore(AuthUser $authUser, Credential $credential): bool
    {
        return $authUser->can('restore_credential');
    }

    public function forceDelete(AuthUser $authUser, Credential $credential): bool
    {
        return $authUser->can('force_delete_credential');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_credential');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_credential');
    }

    public function replicate(AuthUser $authUser, Credential $credential): bool
    {
        return $authUser->can('replicate_credential');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_credential');
    }
}
