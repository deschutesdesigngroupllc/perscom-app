<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Scopes\EnabledScope;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Eloquent
 */
trait CanBeEnabled
{
    public function scopeEnabled(Builder $query): void
    {
        $query->withoutGlobalScope(EnabledScope::class)->where('enabled', true);
    }

    public function scopeDisabled(Builder $query): void
    {
        $query->withoutGlobalScope(EnabledScope::class)->where('enabled', false);
    }

    protected static function bootCanBeEnabled(): void
    {
        static::addGlobalScope(new EnabledScope());
    }

    protected function initializeCanBeEnabled(): void
    {
        $this->mergeFillable(['enabled']);

        $this->mergeCasts([
            'enabled' => 'boolean',
        ]);
    }
}
