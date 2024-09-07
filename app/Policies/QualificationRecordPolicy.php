<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\QualificationRecord;
use App\Models\User;
use Illuminate\Support\Facades\App;

class QualificationRecordPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:qualificationrecord') || optional($user)->tokenCan('view:qualificationrecord');
    }

    public function view(?User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'view:qualificationrecord')
            || $qualification->user?->id === optional($user)->id
            || optional($user)->tokenCan('view:qualificationrecord');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:qualificationrecord') ||
               optional($user)->tokenCan('create:qualificationrecord');
    }

    public function update(?User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'update:qualificationrecord') ||
               optional($user)->tokenCan('update:qualificationrecord');
    }

    public function delete(?User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               optional($user)->tokenCan('delete:qualificationrecord');
    }

    public function restore(?User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               optional($user)->tokenCan('delete:qualificationrecord');
    }

    public function forceDelete(?User $user, QualificationRecord $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualificationrecord') ||
               optional($user)->tokenCan('delete:qualificationrecord');
    }
}
