<?php

namespace App\Policies;

use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class ServiceRecordsPolicy extends Policy
{
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
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user = null)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'view:servicerecord') ||
               $service->user?->id === $user?->id ||
               $user?->tokenCan('view:servicerecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:servicerecord') || $user?->tokenCan('create:servicerecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'update:servicerecord') || $user?->tokenCan('update:servicerecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || $user?->tokenCan('delete:servicerecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || $user?->tokenCan('delete:servicerecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || $user?->tokenCan('delete:servicerecord');
    }
}
