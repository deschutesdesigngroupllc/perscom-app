<?php

namespace App\Policies;

use App\Features\WebhookFeature;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class WebhookPolicy extends Policy
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

        if (Feature::inactive(WebhookFeature::class)) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Webhook $webhook): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Webhook $webhook): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Webhook $webhook): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Webhook $webhook): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Webhook $webhook): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user->tokenCan('manage:webhook');
    }
}
