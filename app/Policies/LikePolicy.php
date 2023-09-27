<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class LikePolicy extends Policy
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
     */
    public function viewAny(User $user = null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user = null, Like $like): bool
    {
        return Gate::check('view', $like->model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user = null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user = null, Like $like): bool
    {
        return Gate::check('update', $like->model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user = null, Like $like): bool
    {
        return Gate::check('delete', $like->model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user = null, Like $like): bool
    {
        return Gate::check('restore', $like->model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user = null, Like $like): bool
    {
        return Gate::check('forceDelete', $like->model);
    }
}
