<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Slot;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasSlot
{
    public function scopeSlot(Builder $query, Slot $slot): void
    {
        $query->whereBelongsTo($slot);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    protected function initializeHasSlot(): void
    {
        $this->mergeFillable([
            'slot_id',
        ]);
    }
}
