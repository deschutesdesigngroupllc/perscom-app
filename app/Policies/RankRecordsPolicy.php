<?php

namespace App\Policies;

use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class RankRecordsPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'view:rankrecord') ||
               $rank->user?->id === $user?->id ||
               $user?->tokenCan('view:rankrecord');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:rankrecord') || $user?->tokenCan('create:rankrecord');
    }

    public function update(User $user = null, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'update:rankrecord') || $user?->tokenCan('update:rankrecord');
    }

    public function delete(User $user = null, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || $user?->tokenCan('delete:rankrecord');
    }

    public function restore(User $user = null, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || $user?->tokenCan('delete:rankrecord');
    }

    public function forceDelete(User $user = null, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || $user?->tokenCan('delete:rankrecord');
    }
}
