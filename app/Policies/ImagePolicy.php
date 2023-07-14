<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class ImagePolicy extends Policy
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
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Image $image): bool
    {
        return Gate::check('view', $image->model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:image') || $user->tokenCan('create:image');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Image $image): bool
    {
        return Gate::check('update', $image->model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Image $image): bool
    {
        return Gate::check('delete', $image->model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Image $image): bool
    {
        return Gate::check('restore', $image->model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Image $image): bool
    {
        return Gate::check('forceDelete', $image->model);
    }
}
