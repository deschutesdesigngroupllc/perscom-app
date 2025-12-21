<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Issuer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class IssuerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_issuer');
    }

    public function view(AuthUser $authUser, Issuer $issuer): bool
    {
        return $authUser->can('view_issuer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_issuer');
    }

    public function update(AuthUser $authUser, Issuer $issuer): bool
    {
        return $authUser->can('update_issuer');
    }

    public function delete(AuthUser $authUser, Issuer $issuer): bool
    {
        return $authUser->can('delete_issuer');
    }

    public function restore(AuthUser $authUser, Issuer $issuer): bool
    {
        return $authUser->can('restore_issuer');
    }

    public function forceDelete(AuthUser $authUser, Issuer $issuer): bool
    {
        return $authUser->can('force_delete_issuer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_issuer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_issuer');
    }

    public function replicate(AuthUser $authUser, Issuer $issuer): bool
    {
        return $authUser->can('replicate_issuer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_issuer');
    }
}
