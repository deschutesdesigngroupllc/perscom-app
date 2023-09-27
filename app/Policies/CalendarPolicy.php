<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class CalendarPolicy extends Policy
{
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
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user = null)
    {
        return $this->hasPermissionTo($user, 'view:calendar') || $user?->tokenCan('view:calendar');
    }

    /**x
     * Determine whether the user can view the model.
     *
     * @param \App\Models\?User $user = null
     * @param \App\Models\Calendar $calendar
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Calendar $calendar)
    {
        return $this->hasPermissionTo($user, 'view:calendar') || $user?->tokenCan('view:calendar');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:calendar') || $user?->tokenCan('create:calendar');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Calendar $calendar)
    {
        return $this->hasPermissionTo($user, 'update:calendar') || $user?->tokenCan('update:calendar');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Calendar $calendar)
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || $user?->tokenCan('delete:calendar');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Calendar $calendar)
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || $user?->tokenCan('delete:calendar');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Calendar $calendar)
    {
        return $this->hasPermissionTo($user, 'delete:calendar') || $user?->tokenCan('delete:calendar');
    }
}
