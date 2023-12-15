<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class ActivityPolicy extends Policy
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
        return true;
    }

    public function view(?User $user, Activity $activity): bool
    {
        return Gate::check('view', $activity->subject);
    }

    public function create(?User $user = null): bool
    {
        return false;
    }

    public function update(?User $user, Activity $activity): bool
    {
        return Gate::check('update', $activity->subject);
    }

    public function delete(?User $user, Activity $activity): bool
    {
        return Gate::check('delete', $activity->subject);
    }

    public function restore(?User $user, Activity $activity): bool
    {
        return Gate::check('restore', $activity->subject);
    }

    public function forceDelete(?User $user, Activity $activity): bool
    {
        return Gate::check('forceDelete', $activity->subject);
    }
}
