<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasUser
{
    /**
     * @param Builder $query
     * @param User    $user
     *
     * @return Builder
     */
    public function scopeForUser(Builder $query, User $user)
    {
        return $query->where('user_id', '=', $user->id);
    }
}
