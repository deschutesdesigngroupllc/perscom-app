<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class CalendarPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:calendar') || optional($user)->tokenCan('view:calendar');
    }

    public function view(?User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'view:calendar') || optional($user)->tokenCan('view:calendar');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:calendar') || optional($user)->tokenCan('create:calendar');
    }

    public function update(?User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'update:calendar') || optional($user)->tokenCan('update:calendar');
    }

    public function delete(?User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || optional($user)->tokenCan('delete:calendar');
    }

    public function restore(?User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || optional($user)->tokenCan('delete:calendar');
    }

    public function forceDelete(?User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || optional($user)->tokenCan('delete:calendar');
    }
}
