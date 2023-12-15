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
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        if (Feature::inactive(WebhookFeature::class)) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return Gate::check('webhook', $user);
    }

    public function view(?User $user, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    public function create(?User $user = null): bool
    {
        return Gate::check('webhook', $user);
    }

    public function update(?User $user, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    public function delete(?User $user, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    public function restore(?User $user, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }

    public function forceDelete(?User $user, Webhook $webhook): bool
    {
        return Gate::check('webhook', $user);
    }
}
