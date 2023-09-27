<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class EventPolicy extends Policy
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
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user = null)
    {
        return $this->hasPermissionTo($user, 'view:event') || $user?->tokenCan('view:event');
    }

    /**x
     * Determine whether the user can view the model.
     *
     * @param \App\Models\?User $user = null
     * @param \App\Models\Event $event
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Event $event)
    {
        return $this->hasPermissionTo($user, 'view:event') ||
               $user?->tokenCan('view:event') ||
               $event->registrations->contains($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:event') || $user?->tokenCan('create:event');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Event $event)
    {
        return $this->hasPermissionTo($user, 'update:event') || $user?->tokenCan('update:event');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Event $event)
    {
        return $this->hasPermissionTo($user, 'delete:event') || $user?->tokenCan('delete:event');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Event $event)
    {
        return $this->hasPermissionTo($user, 'delete:event') || $user?->tokenCan('delete:event');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Event $event)
    {
        return $this->hasPermissionTo($user, 'delete:event') || $user?->tokenCan('delete:event');
    }

    /**
     * Determine where the user can attach to the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function attachAnyUser(User $user = null, Event $event)
    {
        return $this->update($user, $event);
    }

    /**
     * Determine where the user can attach to the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function attachUser(User $user = null, Event $event)
    {
        return $this->update($user, $event);
    }

    /**
     * Determine where the user can attach to the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function detachUser(User $user = null, Event $event)
    {
        return $this->update($user, $event);
    }
}
