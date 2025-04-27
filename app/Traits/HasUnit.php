<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Unit;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasUnit
{
    public function scopeUnit(Builder $query, Unit $unit): void
    {
        $query->whereBelongsTo($unit);
    }

    /**
     * @return BelongsTo<Unit, $this>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    protected function initializeHasUnit(): void
    {
        $this->mergeFillable([
            'unit_id',
        ]);
    }
}
