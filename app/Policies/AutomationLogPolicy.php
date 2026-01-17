<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AutomationLog;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AutomationLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_automation_log');
    }

    public function view(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('view_automation_log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_automation_log');
    }

    public function update(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('update_automation_log');
    }

    public function delete(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('delete_automation_log');
    }

    public function restore(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('restore_automation_log');
    }

    public function forceDelete(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('force_delete_automation_log');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_automation_log');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_automation_log');
    }

    public function replicate(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('replicate_automation_log');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_automation_log');
    }
}
