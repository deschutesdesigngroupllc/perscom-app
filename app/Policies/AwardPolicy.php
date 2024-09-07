<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Award;
use App\Models\User;
use Illuminate\Support\Facades\App;

class AwardPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:award') || optional($user)->tokenCan('view:award');
    }

    public function view(?User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'view:award') || optional($user)->tokenCan('view:award');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:award') || optional($user)->tokenCan('create:award');
    }

    public function update(?User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'update:award') || optional($user)->tokenCan('update:award');
    }

    public function delete(?User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || optional($user)->tokenCan('delete:award');
    }

    public function restore(?User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || optional($user)->tokenCan('delete:award');
    }

    public function forceDelete(?User $user, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || optional($user)->tokenCan('delete:award');
    }
}
