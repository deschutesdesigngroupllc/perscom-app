<?php

namespace App\Policies;

use App\Models\Element;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class ElementPolicy extends Policy
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
    public function view(User $user, Element $element): bool
    {
        return Gate::check('view', $element->model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Element $element): bool
    {
        return Gate::check('update', $element->model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Element $element): bool
    {
        return Gate::check('delete', $element->model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Element $element): bool
    {
        return Gate::check('restore', $element->model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Element $element): bool
    {
        return Gate::check('forceDelete', $element->model);
    }
}
