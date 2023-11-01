<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class EventRegistrationPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, EventRegistration $registration): bool
    {
        return Gate::check('view', $registration->event ?? new Event()) || $registration->user?->id === $user?->id;
    }

    public function create(User $user = null): bool
    {
        return Gate::check('create', Event::class);
    }

    public function update(User $user = null, EventRegistration $registration): bool
    {
        return Gate::check('update', $registration->event ?? new Event());
    }

    public function delete(User $user = null, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }

    public function restore(User $user = null, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }

    public function forceDelete(User $user = null, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }
}
