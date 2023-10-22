<?php

namespace App\Policies;

use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class MailPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:mail') || $user?->tokenCan('view:mail');
    }

    public function view(User $user = null, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'view:mail') || $user?->tokenCan('view:mail');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:mail') || $user?->tokenCan('create:mail');
    }

    public function update(User $user = null, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'update:mail') || $user?->tokenCan('update:mail');
    }

    public function delete(User $user = null, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'delete:mail') || $user?->tokenCan('delete:mail');
    }

    public function restore(User $user = null, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'delete:mail') || $user?->tokenCan('delete:mail');
    }

    public function forceDelete(User $user = null, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'delete:mail') || $user?->tokenCan('delete:mail');
    }
}
