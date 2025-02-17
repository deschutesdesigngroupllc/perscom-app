<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Unit;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasUnit
{
    public function scopeUnit(Builder $query, Unit $unit): void
    {
        $query->whereBelongsTo($unit);
    }

    /**
     * @return BelongsTo<Unit, TModel>
     */
    public function unit(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(Unit::class);
    }

    protected function initializeHasUnit(): void
    {
        $this->mergeFillable([
            'unit_id',
        ]);
    }
}
