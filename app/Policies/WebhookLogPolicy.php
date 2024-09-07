<?php

declare(strict_types=1);

namespace App\Policies;

use App\Features\WebhookFeature;
use App\Models\User;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Laravel\Pennant\Feature;

class WebhookLogPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
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

    public function view(?User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    public function create(?User $user = null): bool
    {
        return false;
    }

    public function update(?User $user, WebhookLog $log): bool
    {
        return false;
    }

    public function delete(?User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    public function restore(?User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }

    public function forceDelete(?User $user, WebhookLog $log): bool
    {
        return Gate::check('webhook', $user);
    }
}
