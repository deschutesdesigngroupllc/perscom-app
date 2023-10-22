<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class FormPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:form') || $user?->tokenCan('view:form');
    }

    public function view(User $user = null, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'view:form') || $user?->tokenCan('view:form');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:form') || $user?->tokenCan('create:form');
    }

    public function update(User $user = null, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'update:form') || $user?->tokenCan('update:form');
    }

    public function delete(User $user = null, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user?->tokenCan('delete:form');
    }

    public function restore(User $user = null, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user?->tokenCan('delete:form');
    }

    public function forceDelete(User $user = null, Form $form): bool
    {
        return $this->hasPermissionTo($user, 'delete:form') || $user?->tokenCan('delete:form');
    }
}
