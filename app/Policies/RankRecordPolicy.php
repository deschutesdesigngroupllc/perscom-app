<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Support\Facades\App;

class RankRecordPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:rankrecord') || optional($user)->tokenCan('view:rankrecord');
    }

    public function view(?User $user, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'view:rankrecord')
            || $rank->user?->id === optional($user)->id
            || optional($user)->tokenCan('view:rankrecord');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:rankrecord') || optional($user)->tokenCan('create:rankrecord');
    }

    public function update(?User $user, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'update:rankrecord') || optional($user)->tokenCan('update:rankrecord');
    }

    public function delete(?User $user, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || optional($user)->tokenCan('delete:rankrecord');
    }

    public function restore(?User $user, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || optional($user)->tokenCan('delete:rankrecord');
    }

    public function forceDelete(?User $user, RankRecord $rank): bool
    {
        return $this->hasPermissionTo($user, 'delete:rankrecord') || optional($user)->tokenCan('delete:rankrecord');
    }
}
