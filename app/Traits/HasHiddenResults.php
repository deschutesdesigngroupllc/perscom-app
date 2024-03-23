<?php

namespace App\Traits;

use Eloquent;
use Illuminate\Contracts\Database\Eloquent\Builder;

/**
 * @mixin Eloquent
 */
trait HasHiddenResults
{
    public function scopeHidden(Builder $query): void
    {
        $query->where('hidden', true);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('hidden', false);
    }
}
