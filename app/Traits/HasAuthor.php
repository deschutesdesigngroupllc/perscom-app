<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasAuthor
{
    public static function bootHasAuthor(): void
    {
        static::creating(function ($model) {
            if ($user = Auth::user()) {
                $model->author()->associate($user);
            }
        });
    }

    public function scopeForAuthor(Builder $query, User $user): Builder
    {
        return $query->whereBelongsTo($user);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
