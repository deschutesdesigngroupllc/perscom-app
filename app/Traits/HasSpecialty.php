<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Specialty;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasSpecialty
{
    public function scopeSpecialty(Builder $query, Specialty $specialty): void
    {
        $query->whereBelongsTo($specialty);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }
}
