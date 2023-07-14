<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class SubmissionPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:submission') || $user->tokenCan('view:submission');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'view:submission') || $user->tokenCan('view:submission');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:submission') || $user->tokenCan('create:submission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'update:submission') || $user->tokenCan('update:submission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || $user->tokenCan('delete:submission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || $user->tokenCan('delete:submission');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || $user->tokenCan('delete:submission');
    }
}
