<?php

namespace App\Policies;

use App\Models\CombatRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class CombatRecordsPolicy extends Policy
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
        return true;
    }

    public function view(User $user = null, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'view:combatrecord') ||
               $combat->user?->id === $user?->id ||
               $user?->tokenCan('view:combatrecord');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:combatrecord') || $user?->tokenCan('create:combatrecord');
    }

    public function update(User $user = null, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'update:combatrecord') || $user?->tokenCan('update:combatrecord');
    }

    public function delete(User $user = null, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'delete:combatrecord') || $user?->tokenCan('delete:combatrecord');
    }

    public function restore(User $user = null, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'delete:combatrecord') || $user?->tokenCan('delete:combatrecord');
    }

    public function forceDelete(User $user = null, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'delete:combatrecord') || $user?->tokenCan('delete:combatrecord');
    }
}
