<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait HasHiddenResults
{
    /**
     * @return void
     */
    public function scopeHidden(Builder $query)
    {
        $query->where('hidden', true);
    }

    /**
     * @return void
     */
    public function scopeVisible(Builder $query)
    {
        $query->where('hidden', false);
    }
}
