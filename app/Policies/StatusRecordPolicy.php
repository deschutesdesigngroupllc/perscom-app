<?php

namespace App\Policies;

use App\Models\StatusRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class StatusRecordPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:statusrecord') || $user->tokenCan('view:statusrecord');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'view:statusrecord') || $user->tokenCan('view:statusrecord')) &&
               Gate::check('view', $statusRecord->model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:statusrecord') || $user->tokenCan('create:statusrecord');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'update:statusrecord') || $user->tokenCan('update:statusrecord')) &&
               Gate::check('update', $statusRecord->model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user->tokenCan('delete:statusrecord')) &&
               Gate::check('delete', $statusRecord->model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user->tokenCan('delete:statusrecord')) &&
               Gate::check('restore', $statusRecord->model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user->tokenCan('delete:statusrecord')) &&
               Gate::check('forceDelete', $statusRecord->model);
    }
}
