<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Request;

class UserPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:user') || $user?->tokenCan('view:user');
    }

    public function view(User $user = null, User $model): bool
    {
        return $this->hasPermissionTo($user, 'view:user') || $user?->id === $model->id || $user?->tokenCan('view:user');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:user') || $user?->tokenCan('create:user');
    }

    public function update(User $user = null, User $model): bool
    {
        return $this->hasPermissionTo($user, 'update:user') ||
               $user?->id === $model->id ||
               $user?->tokenCan('update:user');
    }

    public function delete(User $user = null, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || $user?->tokenCan('delete:user');
    }

    public function restore(User $user = null, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || $user?->tokenCan('delete:user');
    }

    public function forceDelete(User $user = null, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || $user?->tokenCan('delete:user');
    }

    public function impersonate(User $user = null, User $model): bool
    {
        return $this->hasPermissionTo($user, 'impersonate:user') || $user?->tokenCan('impersonate:user');
    }

    public function note(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'note:user') || $user?->tokenCan('note:user');
    }

    public function billing(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:billing') || $user?->tokenCan('manage:billing');
    }

    public function api(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user?->tokenCan('manage:api');
    }

    public function webhook(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || $user?->tokenCan('manage:webhook');
    }
}
