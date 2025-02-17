<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasUser
{
    public function scopeUser(Builder $query, User $user): void
    {
        $query->whereBelongsTo($user);
    }

    /**
     * @return BelongsTo<User, TModel>
     */
    public function user(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(User::class);
    }

    protected function initializeHasUser(): void
    {
        $this->mergeFillable(['user_id']);
    }
}
