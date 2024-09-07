<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Laravel\Cashier\Subscription;

class SubscriptionPolicy extends Policy
{
    public function before(): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function viewAny(?User $user = null): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function view(?User $user, Subscription $subscription): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function create(?User $user = null): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function update(?User $user, Subscription $subscription): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function delete(?User $user, Subscription $subscription): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function restore(?User $user, Subscription $subscription): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function forceDelete(?User $user, Subscription $subscription): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }
}
