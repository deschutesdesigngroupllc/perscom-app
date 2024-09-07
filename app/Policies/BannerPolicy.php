<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Support\Facades\App;

class BannerPolicy extends Policy
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

    public function view(?User $user, Banner $banner): bool
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

    public function update(?User $user, Banner $banner): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function delete(?User $user, Banner $banner): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function restore(?User $user, Banner $banner): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function forceDelete(?User $user, Banner $banner): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }
}
