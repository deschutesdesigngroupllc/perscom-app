<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class CalendarPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return false|void
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'view:calendar') || $user->tokenCan('view:calendar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'view:calendar') || $user->tokenCan('view:calendar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:calendar') || $user->tokenCan('create:calendar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'update:calendar') || $user->tokenCan('update:calendar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || $user->tokenCan('delete:calendar');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || $user->tokenCan('delete:calendar');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Calendar $calendar): bool
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || $user->tokenCan('delete:calendar');
    }
}
