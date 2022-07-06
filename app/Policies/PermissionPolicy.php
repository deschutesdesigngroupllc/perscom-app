<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class PermissionPolicy
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
	    return $user->hasPermissionTo('view:permission');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Permission $permission)
    {
	    return $user->hasPermissionTo('view:permission');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
	    return $user->hasPermissionTo('create:permission');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Permission $permission)
    {
    	if (collect(config('permissions.permissions'))->has($permission->name)) {
    		return false;
	    }
	    return $user->hasPermissionTo('update:permission');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Permission $permission)
    {
	    if (collect(config('permissions.permissions'))->has($permission->name)) {
		    return false;
	    }
	    return $user->hasPermissionTo('delete:permission');
    }

	/**
	 * Determine whether the user can detach the model.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Models\Permission  $permission
	 * @param  \App\Models\Role  $role
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function detachRole(User $user, Permission $permission, Role $role)
	{
		if (collect(config('permissions.roles'))->has($role->name)) {
			return false;
		}
		return $user->hasPermissionTo('update:permission');
	}

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Permission $permission)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Permission $permission)
    {
        //
    }
}
