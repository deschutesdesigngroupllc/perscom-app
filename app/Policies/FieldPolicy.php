<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Field;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class FieldPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_field');
    }

    public function view(AuthUser $authUser, Field $field): bool
    {
        return $authUser->can('view_field');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_field');
    }

    public function update(AuthUser $authUser, Field $field): bool
    {
        return $authUser->can('update_field');
    }

    public function delete(AuthUser $authUser, Field $field): bool
    {
        return $authUser->can('delete_field');
    }

    public function restore(AuthUser $authUser, Field $field): bool
    {
        return $authUser->can('restore_field');
    }

    public function forceDelete(AuthUser $authUser, Field $field): bool
    {
        return $authUser->can('force_delete_field');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_field');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_field');
    }

    public function replicate(AuthUser $authUser, Field $field): bool
    {
        return $authUser->can('replicate_field');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_field');
    }
}
