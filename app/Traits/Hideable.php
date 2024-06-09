<?php

namespace App\Traits;

use App\Models\Scopes\VisibleScope;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Eloquent
 */
trait Hideable
{
    protected static function bootHideable(): void
    {
        static::addGlobalScope(new VisibleScope());
    }

    public function scopeHidden(Builder $query): void
    {
        $query->withoutGlobalScope(VisibleScope::class)->where('hidden', true);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->withoutGlobalScope(VisibleScope::class)->where('hidden', false);
    }
}
