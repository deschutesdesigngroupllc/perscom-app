<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Page;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_page');
    }

    public function view(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('view_page');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_page');
    }

    public function update(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('update_page');
    }

    public function delete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('delete_page');
    }

    public function restore(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('restore_page');
    }

    public function forceDelete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('force_delete_page');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_page');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_page');
    }

    public function replicate(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('replicate_page');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_page');
    }
}
