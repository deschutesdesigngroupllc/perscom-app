<?php

namespace App\Policies;

use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class ServiceRecordsPolicy extends Policy
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

    public function view(User $user = null, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'view:servicerecord') ||
               $service->user?->id === optional($user)->id ||
               optional($user)->tokenCan('view:servicerecord');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:servicerecord') || optional($user)->tokenCan('create:servicerecord');
    }

    public function update(User $user = null, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'update:servicerecord') || optional($user)->tokenCan('update:servicerecord');
    }

    public function delete(User $user = null, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || optional($user)->tokenCan('delete:servicerecord');
    }

    public function restore(User $user = null, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || optional($user)->tokenCan('delete:servicerecord');
    }

    public function forceDelete(User $user = null, ServiceRecord $service): bool
    {
        return $this->hasPermissionTo($user, 'delete:servicerecord') || optional($user)->tokenCan('delete:servicerecord');
    }
}
