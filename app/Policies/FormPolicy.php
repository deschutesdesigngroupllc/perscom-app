<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Form;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class FormPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_form');
    }

    public function view(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('view_form');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_form');
    }

    public function update(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('update_form');
    }

    public function delete(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('delete_form');
    }

    public function restore(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('restore_form');
    }

    public function forceDelete(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('force_delete_form');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_form');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_form');
    }

    public function replicate(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('replicate_form');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_form');
    }
}
