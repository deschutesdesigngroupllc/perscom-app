<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class TenantPolicy extends Policy
{
    public function before(): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function viewAny(?User $user = null): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function view(?User $user, Tenant $tenant): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function create(?User $user = null): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function update(?User $user, Tenant $tenant): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function delete(?User $user, Tenant $tenant): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function restore(?User $user, Tenant $tenant): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function forceDelete(?User $user, Tenant $tenant): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }
}
