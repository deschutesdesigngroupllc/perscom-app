<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Support\Facades\App;

class RankPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:rank') || optional($user)->tokenCan('view:rank');
    }

    public function view(?User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'view:rank') || optional($user)->tokenCan('view:rank');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:rank') || optional($user)->tokenCan('create:rank');
    }

    public function update(?User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'update:rank') || optional($user)->tokenCan('update:rank');
    }

    public function delete(?User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || optional($user)->tokenCan('delete:rank');
    }

    public function restore(?User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || optional($user)->tokenCan('delete:rank');
    }

    public function forceDelete(?User $user, Rank $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rank') || optional($user)->tokenCan('delete:rank');
    }
}
