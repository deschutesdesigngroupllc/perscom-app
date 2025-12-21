<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ApiLog;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ApiLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_api_log');
    }

    public function view(AuthUser $authUser, ApiLog $apiLog): bool
    {
        return $authUser->can('view_api_log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_api_log');
    }

    public function update(AuthUser $authUser, ApiLog $apiLog): bool
    {
        return $authUser->can('update_api_log');
    }

    public function delete(AuthUser $authUser, ApiLog $apiLog): bool
    {
        return $authUser->can('delete_api_log');
    }

    public function restore(AuthUser $authUser, ApiLog $apiLog): bool
    {
        return $authUser->can('restore_api_log');
    }

    public function forceDelete(AuthUser $authUser, ApiLog $apiLog): bool
    {
        return $authUser->can('force_delete_api_log');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_api_log');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_api_log');
    }

    public function replicate(AuthUser $authUser, ApiLog $apiLog): bool
    {
        return $authUser->can('replicate_api_log');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_api_log');
    }
}
