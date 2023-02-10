<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class RolePolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:role') || $user->tokenCan('view:role');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Role $role)
    {
        return $this->hasPermissionTo($user, 'view:role') || $user->tokenCan('view:role');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'create:role') || $user->tokenCan('create:role');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Role $role)
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role') || $user->tokenCan('update:role');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Role $role)
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || $user->tokenCan('delete:role');
    }

    /**
     * Determine whether the user can detach the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @param  \App\Models\Permission  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function detachPermission(User $user, Role $role, Permission $permission)
    {
        if ($role->is_application_role && $permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Role $role)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Role $role)
    {
        //
    }
}
