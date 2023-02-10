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
     * @return bool
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRecord  $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'view:servicerecord') ||
               $service->user?->id === $user->id ||
               $user->tokenCan('view:servicerecord');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:servicerecord') || $user->tokenCan('create:servicerecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRecord  $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'update:servicerecord') || $user->tokenCan('update:servicerecord');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRecord  $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ServiceRecord $service)
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || $user->tokenCan('delete:servicerecord');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRecord  $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ServiceRecord $service)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRecord  $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ServiceRecord $service)
    {
        //
    }
}
