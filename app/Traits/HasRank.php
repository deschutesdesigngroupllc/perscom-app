<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Rank;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasRank
{
    public function scopeRank(Builder $query, Rank $rank): void
    {
        $query->whereBelongsTo($rank);
    }

    /**
     * @return BelongsTo<Rank, TModel>
     */
    public function rank(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(Rank::class);
    }

    protected function initializeHasRank(): void
    {
        $this->mergeFillable([
            'rank_id',
        ]);
    }
}
