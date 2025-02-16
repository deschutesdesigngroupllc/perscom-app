<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Status;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasStatus
{
    public function scopeStatus(Builder $query, Status $status): void
    {
        $query->whereBelongsTo($status);
    }

    /**
     * @return BelongsTo<Status, TModel>
     */
    public function status(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(Status::class);
    }

    protected function initializeHasStatus(): void
    {
        $this->mergeFillable([
            'status_id',
        ]);
    }
}
