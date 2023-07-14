<?php

namespace App\Policies;

use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class ServiceRecordsPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return false|void
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'view:servicerecord') ||
               $service->user?->id === $user->id ||
               $user->tokenCan('view:servicerecord');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:servicerecord') || $user->tokenCan('create:servicerecord');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'update:servicerecord') || $user->tokenCan('update:servicerecord');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || $user->tokenCan('delete:servicerecord');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || $user->tokenCan('delete:servicerecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || $user->tokenCan('delete:servicerecord');
    }
}
