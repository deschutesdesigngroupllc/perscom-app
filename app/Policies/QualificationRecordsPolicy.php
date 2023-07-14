<?php

namespace App\Policies;

use App\Models\QualificationRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class QualificationRecordsPolicy extends Policy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'view:qualificationrecord') ||
               $qualification->user?->id === $user->id ||
               $user->tokenCan('view:qualificationrecord');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:qualificationrecord') ||
               $user->tokenCan('create:qualificationrecord');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'update:qualificationrecord') ||
               $user->tokenCan('update:qualificationrecord');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               $user->tokenCan('delete:qualificationrecord');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               $user->tokenCan('delete:qualificationrecord');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               $user->tokenCan('delete:qualificationrecord');
    }
}
