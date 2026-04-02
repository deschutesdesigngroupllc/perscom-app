<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Padmission\DataLens\Models\CustomReport;

class CustomReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_custom_report');
    }

    public function view(AuthUser $authUser, CustomReport $customReport): bool
    {
        return $authUser->can('view_custom_report');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_custom_report');
    }

    public function update(AuthUser $authUser, CustomReport $customReport): bool
    {
        return $authUser->can('update_custom_report');
    }

    public function delete(AuthUser $authUser, CustomReport $customReport): bool
    {
        return $authUser->can('delete_custom_report');
    }

    public function restore(AuthUser $authUser, CustomReport $customReport): bool
    {
        return $authUser->can('restore_custom_report');
    }

    public function forceDelete(AuthUser $authUser, CustomReport $customReport): bool
    {
        return $authUser->can('force_delete_custom_report');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_custom_report');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_custom_report');
    }

    public function replicate(AuthUser $authUser, CustomReport $customReport): bool
    {
        return $authUser->can('replicate_custom_report');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_custom_report');
    }

    public function manageApi(User $user, CustomReport $customReport): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }

    public function manageSchedules(User $user, CustomReport $customReport): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }

    public function export(User $user, CustomReport $customReport): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }
}
