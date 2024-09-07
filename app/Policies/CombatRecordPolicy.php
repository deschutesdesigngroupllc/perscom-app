<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CombatRecord;
use App\Models\User;
use Illuminate\Support\Facades\App;

class CombatRecordPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:combatrecord') || optional($user)->tokenCan('view:combatrecord');
    }

    public function view(?User $user, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'view:combatrecord')
            || $combat->user?->id === optional($user)->id
            || optional($user)->tokenCan('view:combatrecord');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:combatrecord') || optional($user)->tokenCan('create:combatrecord');
    }

    public function update(?User $user, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'update:combatrecord') || optional($user)->tokenCan('update:combatrecord');
    }

    public function delete(?User $user, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'delete:combatrecord') || optional($user)->tokenCan('delete:combatrecord');
    }

    public function restore(?User $user, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'delete:combatrecord') || optional($user)->tokenCan('delete:combatrecord');
    }

    public function forceDelete(?User $user, CombatRecord $combat): bool
    {
        return $this->hasPermissionTo($user, 'delete:combatrecord') || optional($user)->tokenCan('delete:combatrecord');
    }
}
