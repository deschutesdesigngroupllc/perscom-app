<?php

namespace App\Policies;

use App\Models\AwardRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AwardRecordsPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'view:awardrecord') ||
               $award->user?->id === $user?->id ||
               $user?->tokenCan('view:awardrecord');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:awardrecord') || $user?->tokenCan('create:awardrecord');
    }

    public function update(User $user = null, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'update:awardrecord') || $user?->tokenCan('update:awardrecord');
    }

    public function delete(User $user = null, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user?->tokenCan('delete:awardrecord');
    }

    public function restore(User $user = null, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user?->tokenCan('delete:awardrecord');
    }

    public function forceDelete(User $user = null, AwardRecord $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:awardrecord') || $user?->tokenCan('delete:awardrecord');
    }
}
