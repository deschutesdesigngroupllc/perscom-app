<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class BannerPolicy extends Policy
{
    public function before(): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function viewAny(User $user = null): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function view(User $user = null, Banner $banner): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function create(User $user = null): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function update(User $user = null, Banner $banner): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function delete(User $user = null, Banner $banner): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function restore(User $user = null, Banner $banner): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function forceDelete(User $user = null, Banner $banner): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }
}
