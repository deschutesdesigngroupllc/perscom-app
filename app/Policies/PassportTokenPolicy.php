<?php

declare(strict_types=1);

namespace App\Policies;

use App\Features\ApiAccessFeature;
use App\Models\PassportToken;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Laravel\Pennant\Feature;

class PassportTokenPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        if (Feature::inactive(ApiAccessFeature::class)) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return Gate::check('api', $user);
    }

    public function view(?User $user, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function create(?User $user = null): bool
    {
        return Gate::check('api', $user);
    }

    public function update(?User $user, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function delete(?User $user, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function restore(?User $user, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function forceDelete(?User $user, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }
}
