<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class EventPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:event') || $user->tokenCan('view:event');
    }

    /**x
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Event $event
     */
    public function view(User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'view:event') ||
               $user->tokenCan('view:event') ||
               $event->registrations->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:event') || $user->tokenCan('create:event');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'update:event') || $user->tokenCan('update:event');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'delete:event') || $user->tokenCan('delete:event');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'delete:event') || $user->tokenCan('delete:event');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return $this->hasPermissionTo($user, 'delete:event') || $user->tokenCan('delete:event');
    }

    /**
     * Determine where the user can attach to the model.
     */
    public function attachAnyUser(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    /**
     * Determine where the user can attach to the model.
     */
    public function attachUser(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    /**
     * Determine where the user can attach to the model.
     */
    public function detachUser(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }
}
