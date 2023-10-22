<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUser
{
    public function scopeUser(Builder $query, User $user): void
    {
        $query->whereBelongsTo($user);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
