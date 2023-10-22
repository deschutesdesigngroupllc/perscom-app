<?php

namespace App\Policies;

use App\Features\WebhookFeature;
use App\Models\User;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class WebhookLogPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        if (Feature::inactive(WebhookFeature::class)) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return Gate::check('webhook', $user);
    }

    public function view(User $user = null, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    public function create(User $user = null): bool
    {
        return false;
    }

    public function update(User $user = null, WebhookLog $log): bool
    {
        return false;
    }

    public function delete(User $user = null, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    public function restore(User $user = null, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    public function forceDelete(User $user = null, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }
}
