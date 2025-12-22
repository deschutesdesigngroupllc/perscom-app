<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_category');
    }

    public function view(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('view_category');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_category');
    }

    public function update(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('update_category');
    }

    public function delete(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('delete_category');
    }

    public function restore(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('restore_category');
    }

    public function forceDelete(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('force_delete_category');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_category');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_category');
    }

    public function replicate(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('replicate_category');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_category');
    }
}
