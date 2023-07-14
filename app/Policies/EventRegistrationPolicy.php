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
    public function view(User $user, EventRegistration $registration): bool
    {
        return Gate::check('view', $registration->event ?? new Event()) || $registration->user?->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Gate::check('create', Event::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EventRegistration $registration): bool
    {
        return Gate::check('update', $registration->event ?? new Event());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }
}
