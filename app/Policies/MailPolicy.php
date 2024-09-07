<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\App;

class MailPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:mail') || optional($user)->tokenCan('view:mail');
    }

    public function view(?User $user, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'view:mail') || optional($user)->tokenCan('view:mail');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:mail') || optional($user)->tokenCan('create:mail');
    }

    public function update(?User $user, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'update:mail') || optional($user)->tokenCan('update:mail');
    }

    public function delete(?User $user, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'delete:mail') || optional($user)->tokenCan('delete:mail');
    }

    public function restore(?User $user, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'delete:mail') || optional($user)->tokenCan('delete:mail');
    }

    public function forceDelete(?User $user, Mail $mail): bool
    {
        return $this->hasPermissionTo($user, 'delete:mail') || optional($user)->tokenCan('delete:mail');
    }
}
