<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class EventRegistrationPolicy extends Policy
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
     * @param  \App\Models\EventRegistration  $registration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EventRegistration $registration)
    {
        return Gate::check('view', $registration->event ?? Event::make()) || $registration->user?->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return Gate::check('create', Event::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventRegistration  $registration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EventRegistration $registration)
    {
        return Gate::check('update', $registration->event ?? Event::make());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventRegistration  $registration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EventRegistration $registration)
    {
        return Gate::check('delete', $registration->event ?? Event::make());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventRegistration  $registration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EventRegistration $registration)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EventRegistration  $registration
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EventRegistration $registration)
    {
        //
    }
}
