<?php

namespace App\Policies;

use App\Models\Field;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class FieldPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:field') || $user?->tokenCan('view:field');
    }

    public function view(User $user = null, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'view:field') || $user?->tokenCan('view:field');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:field') || $user?->tokenCan('create:field');
    }

    public function update(User $user = null, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'update:field') || $user?->tokenCan('update:field');
    }

    public function delete(User $user = null, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user?->tokenCan('delete:field');
    }

    public function restore(User $user = null, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user?->tokenCan('delete:field');
    }

    public function forceDelete(User $user = null, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || $user?->tokenCan('delete:field');
    }
}
