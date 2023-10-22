<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

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
