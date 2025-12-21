<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ServiceRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_service_record');
    }

    public function view(AuthUser $authUser, ServiceRecord $serviceRecord): bool
    {
        return $authUser->can('view_service_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_service_record');
    }

    public function update(AuthUser $authUser, ServiceRecord $serviceRecord): bool
    {
        return $authUser->can('update_service_record');
    }

    public function delete(AuthUser $authUser, ServiceRecord $serviceRecord): bool
    {
        return $authUser->can('delete_service_record');
    }

    public function restore(AuthUser $authUser, ServiceRecord $serviceRecord): bool
    {
        return $authUser->can('restore_service_record');
    }

    public function forceDelete(AuthUser $authUser, ServiceRecord $serviceRecord): bool
    {
        return $authUser->can('force_delete_service_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_service_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_service_record');
    }

    public function replicate(AuthUser $authUser, ServiceRecord $serviceRecord): bool
    {
        return $authUser->can('replicate_service_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_service_record');
    }
}
