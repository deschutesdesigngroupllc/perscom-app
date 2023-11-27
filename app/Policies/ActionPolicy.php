<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Laravel\Nova\Actions\ActionEvent;

class ActionPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:log') || optional($user)->tokenCan('view:log');
    }

    public function view(User $user = null, ActionEvent $actionEvent): bool
    {
        return $this->hasPermissionTo($user, 'view:log') || optional($user)->tokenCan('view:log');
    }
}
