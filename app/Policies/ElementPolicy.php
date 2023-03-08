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
     * @param  \App\Models\Element  $element
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Element $element)
    {
        return Gate::check('view', $element->model);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Element  $element
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Element $element)
    {
        return Gate::check('update', $element->model);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Element  $element
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Element $element)
    {
        return Gate::check('delete', $element->model);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Element  $element
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Element $element)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Element  $element
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Element $element)
    {
        //
    }
}
