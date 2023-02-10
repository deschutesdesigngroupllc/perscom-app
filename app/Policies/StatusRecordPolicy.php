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
        return $this->hasPermissionTo($user, 'view:statusrecord') || $user->tokenCan('view:statusrecord');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatusRecord  $statusRecord
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'view:statusrecord') || $user->tokenCan('view:statusrecord')) &&
               Gate::check('view', $statusRecord->model);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:statusrecord') || $user->tokenCan('create:statusrecord');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatusRecord  $statusRecord
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'update:statusrecord') || $user->tokenCan('update:statusrecord')) &&
               Gate::check('update', $statusRecord->model);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatusRecord  $statusRecord
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, StatusRecord $statusRecord)
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user->tokenCan('delete:statusrecord')) &&
               Gate::check('delete', $statusRecord->model);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatusRecord  $statusRecord
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, StatusRecord $statusRecord)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatusRecord  $statusRecord
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, StatusRecord $statusRecord)
    {
        //
    }
}
