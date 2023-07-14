<?php

namespace App\Policies;

use App\Features\WebhookFeature;
use App\Models\User;
use App\Models\WebhookLog;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class WebhookLogPolicy extends Policy
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
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WebhookLog $log): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }
}
