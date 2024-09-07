<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Scopes\VisibleScope;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Eloquent
 */
trait CanBeHidden
{
    public function scopeHidden(Builder $query): void
    {
        $query->withoutGlobalScope(VisibleScope::class)->where('hidden', true);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->withoutGlobalScope(VisibleScope::class)->where('hidden', false);
    }

    protected static function bootCanBeHidden(): void
    {
        static::addGlobalScope(new VisibleScope);
    }

    protected function initializeCanBeHidden(): void
    {
        $this->mergeFillable(['hidden']);

        $this->mergeCasts([
            'hidden' => 'boolean',
        ]);
    }
}
