<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn (Admin|User|null $user = null): bool => match (config('tenancy.enabled')) {
            true => $user instanceof Admin,
            false => $user->hasRole(Utils::getSuperAdminName())
        });
    }
}
