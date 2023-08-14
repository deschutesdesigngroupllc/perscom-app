<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasUser
{
    /**
     * @return Builder
     */
    public function scopeUser(Builder $query, User $user)
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
