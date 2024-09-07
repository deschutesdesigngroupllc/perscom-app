<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Support\Facades\App;

class ServiceRecordPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:servicerecord') || optional($user)->tokenCan('view:servicerecord');
    }

    public function view(?User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'view:servicerecord')
            || $service->user?->id === optional($user)->id
            || optional($user)->tokenCan('view:servicerecord');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:servicerecord') || optional($user)->tokenCan('create:servicerecord');
    }

    public function update(?User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'update:servicerecord') || optional($user)->tokenCan('update:servicerecord');
    }

    public function delete(?User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || optional($user)->tokenCan('delete:servicerecord');
    }

    public function restore(?User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || optional($user)->tokenCan('delete:servicerecord');
    }

    public function forceDelete(?User $user, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || optional($user)->tokenCan('delete:servicerecord');
    }
}
