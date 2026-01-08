<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Calendar;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CalendarPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_calendar');
    }

    public function view(AuthUser $authUser, Calendar $calendar): bool
    {
        return $authUser->can('view_calendar');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_calendar');
    }

    public function update(AuthUser $authUser, Calendar $calendar): bool
    {
        return $authUser->can('update_calendar');
    }

    public function delete(AuthUser $authUser, Calendar $calendar): bool
    {
        return $authUser->can('delete_calendar');
    }

    public function restore(AuthUser $authUser, Calendar $calendar): bool
    {
        return $authUser->can('restore_calendar');
    }

    public function forceDelete(AuthUser $authUser, Calendar $calendar): bool
    {
        return $authUser->can('force_delete_calendar');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_calendar');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_calendar');
    }

    public function replicate(AuthUser $authUser, Calendar $calendar): bool
    {
        return $authUser->can('replicate_calendar');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_calendar');
    }
}
