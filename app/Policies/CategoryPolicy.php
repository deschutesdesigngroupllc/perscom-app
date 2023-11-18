<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class CategoryPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:category') || $user?->tokenCan('view:category');
    }

    public function view(User $user = null, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'view:category') || $user?->tokenCan('view:category');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:category') || $user?->tokenCan('create:category');
    }

    public function update(User $user = null, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'update:category') || $user?->tokenCan('update:category');
    }

    public function delete(User $user = null, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'delete:category') || $user?->tokenCan('delete:category');
    }

    public function restore(User $user = null, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'delete:category') || $user?->tokenCan('delete:category');
    }

    public function forceDelete(User $user = null, Category $category): bool
    {
        return $this->hasPermissionTo($user, 'delete:category') || $user?->tokenCan('delete:category');
    }
}
