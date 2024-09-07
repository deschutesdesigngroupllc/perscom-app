<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class EventRegistrationPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, EventRegistration $registration): bool
    {
        return Gate::check('view', $registration->event ?? new Event()) || optional($registration->user)->id === optional($user)->id;
    }

    public function create(?User $user = null): bool
    {
        return Gate::check('create', Event::class);
    }

    public function update(?User $user, EventRegistration $registration): bool
    {
        return Gate::check('update', $registration->event ?? new Event());
    }

    public function delete(?User $user, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }

    public function restore(?User $user, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }

    public function forceDelete(?User $user, EventRegistration $registration): bool
    {
        return Gate::check('delete', $registration->event ?? new Event());
    }
}
