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

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:user') || optional($user)->tokenCan('view:user');
    }

    public function view(?User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'view:user')
            || optional($user)->id === $model->id
            || optional($user)->tokenCan('view:user');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:user') || optional($user)->tokenCan('create:user');
    }

    public function update(?User $user, User $model): bool
    {
        if (Request::isDemoMode() && $model->email === config('demo.email')) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:user')
            || optional($user)->id === $model->id
            || optional($user)->tokenCan('update:user');
    }

    public function delete(?User $user, User $model): bool
    {
        if (Request::isDemoMode() && $model->email === config('demo.email')) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:user') || optional($user)->tokenCan('delete:user');
    }

    public function restore(?User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || optional($user)->tokenCan('delete:user');
    }

    public function forceDelete(?User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'delete:user') || optional($user)->tokenCan('delete:user');
    }

    public function impersonate(?User $user, User $model): bool
    {
        return $this->hasPermissionTo($user, 'impersonate:user') || optional($user)->tokenCan('impersonate:user');
    }

    public function note(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'note:user') || optional($user)->tokenCan('note:user');
    }

    public function newsfeed(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || optional($user)->tokenCan('manage:newsfeed');
    }

    public function billing(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:billing') || optional($user)->tokenCan('manage:billing');
    }

    public function api(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:api') || optional($user)->tokenCan('manage:api');
    }

    public function webhook(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:webhook') || optional($user)->tokenCan('manage:webhook');
    }
}
