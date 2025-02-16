<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Specialty;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasSpecialty
{
    public function scopeSpecialty(Builder $query, Specialty $specialty): void
    {
        $query->whereBelongsTo($specialty);
    }

    /**
     * @return BelongsTo<Specialty, TModel>
     */
    public function specialty(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(Specialty::class);
    }

    protected function initializeHasSpecialty(): void
    {
        $this->mergeFillable([
            'specialty_id',
        ]);
    }
}
