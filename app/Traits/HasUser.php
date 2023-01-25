<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasUser
{
    /**
     * @param  Builder  $query
     * @param  User  $user
     * @return Builder
     */
    public function scopeForUser(Builder $query, User $user)
    {
        return $query->whereBelongsTo($user);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
