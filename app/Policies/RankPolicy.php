<?php

namespace App\Policies;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class RankPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:rank') || $user?->tokenCan('view:rank');
    }

    public function view(User $user = null, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'view:rank') || $user?->tokenCan('view:rank');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:rank') || $user?->tokenCan('create:rank');
    }

    public function update(User $user = null, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'update:rank') || $user?->tokenCan('update:rank');
    }

    public function delete(User $user = null, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user?->tokenCan('delete:rank');
    }

    public function restore(User $user = null, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user?->tokenCan('delete:rank');
    }

    public function forceDelete(User $user = null, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || $user?->tokenCan('delete:rank');
    }
}
