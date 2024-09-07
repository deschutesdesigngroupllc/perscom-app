<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Support\Facades\App;

class AdminPolicy extends Policy
{
    public function before(): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function viewAny(Admin $admin): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function view(Admin $admin, Admin $model): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function create(Admin $admin): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function update(Admin $admin, Admin $model): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function delete(Admin $admin, Admin $model): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function restore(Admin $admin, Admin $model): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }

    public function forceDelete(Admin $admin, Admin $model): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }
}
