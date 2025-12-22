<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PassportToken;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PassportTokenPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_passport_token');
    }

    public function view(AuthUser $authUser, PassportToken $passportToken): bool
    {
        return $authUser->can('view_passport_token');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_passport_token');
    }

    public function update(AuthUser $authUser, PassportToken $passportToken): bool
    {
        return $authUser->can('update_passport_token');
    }

    public function delete(AuthUser $authUser, PassportToken $passportToken): bool
    {
        return $authUser->can('delete_passport_token');
    }

    public function restore(AuthUser $authUser, PassportToken $passportToken): bool
    {
        return $authUser->can('restore_passport_token');
    }

    public function forceDelete(AuthUser $authUser, PassportToken $passportToken): bool
    {
        return $authUser->can('force_delete_passport_token');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_passport_token');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_passport_token');
    }

    public function replicate(AuthUser $authUser, PassportToken $passportToken): bool
    {
        return $authUser->can('replicate_passport_token');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_passport_token');
    }
}
