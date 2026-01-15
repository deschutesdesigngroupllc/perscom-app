<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Automation;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AutomationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_automation');
    }

    public function view(AuthUser $authUser, Automation $automation): bool
    {
        return $authUser->can('view_automation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_automation');
    }

    public function update(AuthUser $authUser, Automation $automation): bool
    {
        return $authUser->can('update_automation');
    }

    public function delete(AuthUser $authUser, Automation $automation): bool
    {
        return $authUser->can('delete_automation');
    }

    public function restore(AuthUser $authUser, Automation $automation): bool
    {
        return $authUser->can('restore_automation');
    }

    public function forceDelete(AuthUser $authUser, Automation $automation): bool
    {
        return $authUser->can('force_delete_automation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_automation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_automation');
    }

    public function replicate(AuthUser $authUser, Automation $automation): bool
    {
        return $authUser->can('replicate_automation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_automation');
    }
}
