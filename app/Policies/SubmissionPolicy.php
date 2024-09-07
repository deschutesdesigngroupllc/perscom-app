<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\App;

class SubmissionPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:submission') || optional($user)->tokenCan('view:submission');
    }

    public function view(?User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'view:submission') || optional($user)->tokenCan('view:submission');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:submission') || optional($user)->tokenCan('create:submission');
    }

    public function update(?User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'update:submission') || optional($user)->tokenCan('update:submission');
    }

    public function delete(?User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || optional($user)->tokenCan('delete:submission');
    }

    public function restore(?User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || optional($user)->tokenCan('delete:submission');
    }

    public function forceDelete(?User $user, Submission $submission): bool
    {
        return $this->hasPermissionTo($user, 'delete:submission') || optional($user)->tokenCan('delete:submission');
    }
}
