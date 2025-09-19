<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Auth\Access\HandlesAuthorization;
use Padmission\DataLens\Models\CustomReport;

class CustomReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }

    public function view(User $user, CustomReport $customReport): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }

    public function update(User $user, CustomReport $customReport): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }

    public function delete(User $user, CustomReport $customReport): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
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
