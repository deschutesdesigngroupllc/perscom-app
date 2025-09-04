<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Rank;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasRank
{
    public function scopeRank(Builder $query, Rank $rank): void
    {
        $query->whereBelongsTo($rank);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    protected function initializeHasRank(): void
    {
        $this->mergeFillable([
            'rank_id',
        ]);
    }
}
