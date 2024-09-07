<?php

declare(strict_types=1);

namespace App\Features;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class BillingFeature extends BaseFeature
{
    public function before(): ?bool
    {
        if (App::isDemo() || App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function resolve(?string $scope): bool
    {
        /** @var User|null $user */
        $user = request()->user();

        return match (true) {
            $user->hasRole('Admin') => true,
            Gate::check('billing', $user) => true,
            default => false,
        };
    }
}
