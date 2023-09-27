<?php

namespace App\Policies;

use App\Models\StatusRecord;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class StatusRecordPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:statusrecord') || $user?->tokenCan('view:statusrecord');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'view:statusrecord') || $user?->tokenCan('view:statusrecord')) &&
               Gate::check('view', $statusRecord->model);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:statusrecord') || $user?->tokenCan('create:statusrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'update:statusrecord') || $user?->tokenCan('update:statusrecord')) &&
               Gate::check('update', $statusRecord->model);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user?->tokenCan('delete:statusrecord')) &&
               Gate::check('delete', $statusRecord->model);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user?->tokenCan('delete:statusrecord')) &&
               Gate::check('restore', $statusRecord->model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user?->tokenCan('delete:statusrecord')) &&
               Gate::check('forceDelete', $statusRecord->model);
    }
}
