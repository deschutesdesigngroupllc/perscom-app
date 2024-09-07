<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\App;

class EventPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:event') || optional($user)->tokenCan('view:event');
    }

    public function view(?User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'view:event') ||
               optional($user)->tokenCan('view:event') ||
               $event->registrations->contains($user);
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:event') || optional($user)->tokenCan('create:event');
    }

    public function update(?User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'update:event') || optional($user)->tokenCan('update:event');
    }

    public function delete(?User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'delete:event') || optional($user)->tokenCan('delete:event');
    }

    public function restore(?User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'delete:event') || optional($user)->tokenCan('delete:event');
    }

    public function forceDelete(?User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'delete:event') || optional($user)->tokenCan('delete:event');
    }

    public function attachAnyUser(?User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function attachUser(?User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function detachUser(?User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function onEventClick()
    {
        return true;
    }
}
