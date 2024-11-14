<?php

declare(strict_types=1);

namespace App\Features;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class BillingFeature extends BaseFeature
{
    public function before(): ?bool
    {
        if ((App::isDemo() || App::isAdmin()) && ! App::runningInConsole()) {
            return false;
        }

        return null;
    }

    public function resolve(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return match (true) {
            $user->hasRole(Utils::getSuperAdminName()) => true,
            default => false,
        };
    }
}
