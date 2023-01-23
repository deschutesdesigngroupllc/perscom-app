<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class SubmissionPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view:submission', 'web') || $user->tokenCan('view:submission');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Submission $submission)
    {
        return $user->hasPermissionTo('view:submission', 'web') ||
               $user->id === $submission->user?->id ||
               $user->tokenCan('view:submission');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create:submission', 'web') || $user->tokenCan('create:submission');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Submission $submission)
    {
        return $user->hasPermissionTo('update:submission', 'web') || $user->tokenCan('update:submission');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Submission $submission)
    {
        return $user->hasPermissionTo('delete:submission', 'web') || $user->tokenCan('delete:submission');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Submission $submission)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Submission $submission)
    {
        //
    }
}
