<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Slot;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SlotPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_slot');
    }

    public function view(AuthUser $authUser, Slot $slot): bool
    {
        return $authUser->can('view_slot');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_slot');
    }

    public function update(AuthUser $authUser, Slot $slot): bool
    {
        return $authUser->can('update_slot');
    }

    public function delete(AuthUser $authUser, Slot $slot): bool
    {
        return $authUser->can('delete_slot');
    }

    public function restore(AuthUser $authUser, Slot $slot): bool
    {
        return $authUser->can('restore_slot');
    }

    public function forceDelete(AuthUser $authUser, Slot $slot): bool
    {
        return $authUser->can('force_delete_slot');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_slot');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_slot');
    }

    public function replicate(AuthUser $authUser, Slot $slot): bool
    {
        return $authUser->can('replicate_slot');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_slot');
    }
}
