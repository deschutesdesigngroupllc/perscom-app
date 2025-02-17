<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Position;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasPosition
{
    public function scopePosition(Builder $query, Position $position): void
    {
        $query->whereBelongsTo($position);
    }

    /**
     * @return BelongsTo<Position, TModel>
     */
    public function position(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(Position::class);
    }

    protected function initializeHasPosition(): void
    {
        $this->mergeFillable([
            'position_id',
        ]);
    }
}
