<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Status;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasStatus
{
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    protected function initializeHasStatus(): void
    {
        $this->mergeFillable([
            'status_id',
        ]);
    }
}
