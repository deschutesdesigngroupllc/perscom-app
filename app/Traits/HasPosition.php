<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Position;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasPosition
{
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    protected function scopePosition(Builder $query, Position $position): void
    {
        $query->whereBelongsTo($position);
    }

    protected function initializeHasPosition(): void
    {
        $this->mergeFillable([
            'position_id',
        ]);
    }
}
