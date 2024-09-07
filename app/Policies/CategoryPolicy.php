<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\App;

class CategoryPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:category') || optional($user)->tokenCan('view:category');
    }

    public function view(?User $user, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'view:category') || optional($user)->tokenCan('view:category');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:category') || optional($user)->tokenCan('create:category');
    }

    public function update(?User $user, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'update:category') || optional($user)->tokenCan('update:category');
    }

    public function delete(?User $user, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'delete:category') || optional($user)->tokenCan('delete:category');
    }

    public function restore(?User $user, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'delete:category') || optional($user)->tokenCan('delete:category');
    }

    public function forceDelete(?User $user, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'delete:category') || optional($user)->tokenCan('delete:category');
    }
}
