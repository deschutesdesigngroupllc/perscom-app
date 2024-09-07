<?php

declare(strict_types=1);

namespace App\Policies;

use App\Features\ApiAccessFeature;
use App\Models\ApiLog;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Laravel\Pennant\Feature;

class ApiLogPolicy extends Policy
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

    public function view(?User $user, ApiLog $log): bool
    {
        return Gate::check('api', $user);
    }

    public function create(?User $user = null): bool
    {
        return false;
    }

    public function update(?User $user, ApiLog $log): bool
    {
        return false;
    }

    public function delete(?User $user, ApiLog $log): bool
    {
        return Gate::check('api', $user);
    }

    public function restore(?User $user, ApiLog $log): bool
    {
        return Gate::check('api', $user);
    }

    public function forceDelete(?User $user, ApiLog $log): bool
    {
        return Gate::check('api', $user);
    }
}
