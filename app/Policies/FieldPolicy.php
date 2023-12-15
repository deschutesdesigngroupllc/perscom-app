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

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:field') || optional($user)->tokenCan('view:field');
    }

    public function view(?User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'view:field') || optional($user)->tokenCan('view:field');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:field') || optional($user)->tokenCan('create:field');
    }

    public function update(?User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'update:field') || optional($user)->tokenCan('update:field');
    }

    public function delete(?User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || optional($user)->tokenCan('delete:field');
    }

    public function restore(?User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || optional($user)->tokenCan('delete:field');
    }

    public function forceDelete(?User $user, Field $field): bool
    {
        return $this->hasPermissionTo($user, 'delete:field') || optional($user)->tokenCan('delete:field');
    }
}
