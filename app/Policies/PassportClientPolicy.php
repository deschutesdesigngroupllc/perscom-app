<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PassportClient;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PassportClientPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_passport_client');
    }

    public function view(AuthUser $authUser, PassportClient $passportClient): bool
    {
        return $authUser->can('view_passport_client');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_passport_client');
    }

    public function update(AuthUser $authUser, PassportClient $passportClient): bool
    {
        return $authUser->can('update_passport_client');
    }

    public function delete(AuthUser $authUser, PassportClient $passportClient): bool
    {
        return $authUser->can('delete_passport_client');
    }

    public function restore(AuthUser $authUser, PassportClient $passportClient): bool
    {
        return $authUser->can('restore_passport_client');
    }

    public function forceDelete(AuthUser $authUser, PassportClient $passportClient): bool
    {
        return $authUser->can('force_delete_passport_client');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_passport_client');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_passport_client');
    }

    public function replicate(AuthUser $authUser, PassportClient $passportClient): bool
    {
        return $authUser->can('replicate_passport_client');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_passport_client');
    }
}
