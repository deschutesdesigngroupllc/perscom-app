<?php

namespace App\Policies;

use App\Features\WebhookFeature;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class WebhookPolicy extends Policy
{
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
    public function viewAny(User $user = null): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user = null, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user = null): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user = null, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user = null, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user = null, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user = null, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }
}
