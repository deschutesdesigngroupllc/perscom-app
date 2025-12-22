<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CompetencyCategory;
use App\Models\User;

class CompetencyCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, CompetencyCategory $competencyCategory): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, CompetencyCategory $competencyCategory): bool
    {
        return false;
    }

    public function delete(User $user, CompetencyCategory $competencyCategory): bool
    {
        return false;
    }

    public function restore(User $user, CompetencyCategory $competencyCategory): bool
    {
        return false;
    }

    public function forceDelete(User $user, CompetencyCategory $competencyCategory): bool
    {
        return false;
    }
}
