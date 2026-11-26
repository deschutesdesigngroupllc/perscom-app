<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Position;
use Eloquent;
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

    protected function initializeHasPosition(): void
    {
        $this->mergeFillable([
            'position_id',
        ]);
    }
}
