<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class SubmissionPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:submission') || $user?->tokenCan('view:submission');
    }

    public function view(User $user = null, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'view:submission') || $user?->tokenCan('view:submission');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:submission') || $user?->tokenCan('create:submission');
    }

    public function update(User $user = null, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'update:submission') || $user?->tokenCan('update:submission');
    }

    public function delete(User $user = null, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || $user?->tokenCan('delete:submission');
    }

    public function restore(User $user = null, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || $user?->tokenCan('delete:submission');
    }

    public function forceDelete(User $user = null, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || $user?->tokenCan('delete:submission');
    }
}
