<?php

namespace App\Policies;

use App\Models\StatusRecord;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class StatusRecordPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:statusrecord') || $user?->tokenCan('view:statusrecord');
    }

    public function view(User $user = null, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'view:statusrecord') || $user?->tokenCan('view:statusrecord')) &&
               Gate::check('view', $statusRecord->model);
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:statusrecord') || $user?->tokenCan('create:statusrecord');
    }

    public function update(User $user = null, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'update:statusrecord') || $user?->tokenCan('update:statusrecord')) &&
               Gate::check('update', $statusRecord->model);
    }

    public function delete(User $user = null, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user?->tokenCan('delete:statusrecord')) &&
               Gate::check('delete', $statusRecord->model);
    }

    public function restore(User $user = null, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user?->tokenCan('delete:statusrecord')) &&
               Gate::check('restore', $statusRecord->model);
    }

    public function forceDelete(User $user = null, StatusRecord $statusRecord): bool
    {
        return ($this->hasPermissionTo($user, 'delete:statusrecord') || $user?->tokenCan('delete:statusrecord')) &&
               Gate::check('forceDelete', $statusRecord->model);
    }
}
