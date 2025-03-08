<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasAuthor
{
    public function scopeAuthor(Builder $query, User $user): void
    {
        $query->whereBelongsTo($user);
    }

    /**
     * @return BelongsTo<User, TModel>
     */
    public function author(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(User::class, 'author_id');
    }

    protected static function bootHasAuthor(): void
    {
        static::creating(function ($model): void {
            if (($user = Auth::user()) && ! $model->author_id) {
                /** @var User $user */
                $model->author()->associate($user);
            }
        });
    }

    protected function initializeHasAuthor(): void
    {
        $this->mergeFillable(['author_id']);
    }
}
