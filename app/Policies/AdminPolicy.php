<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Support\Facades\Request;

class AdminPolicy extends Policy
{
    public function before(): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function viewAny(Admin $admin): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function view(Admin $admin, Admin $model): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function create(Admin $admin): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function update(Admin $admin, Admin $model): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function delete(Admin $admin, Admin $model): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function restore(Admin $admin, Admin $model): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function forceDelete(Admin $admin, Admin $model): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }
}
