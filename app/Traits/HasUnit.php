<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Unit;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasUnit
{
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
