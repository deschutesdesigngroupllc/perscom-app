<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Tenant;
use Eloquent;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @mixin Eloquent
 */
trait HasResourceUrl
{
    public function url(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->getKey(), fn () => optional(tenant(), fn (Tenant $tenant) => optional(Filament::getModelResource($this), function ($class) use ($tenant) {
                /** @var class-string<resource> $class */
                if ($class::hasPage('view')) {
                    return $class::getUrl('view', [
                        'record' => $this,
                        'tenant' => $tenant,
                    ], panel: 'app');
                }

                /** @var class-string<resource> $class */
                if ($class::hasPage('edit')) {
                    return $class::getUrl('edit', [
                        'record' => $this,
                        'tenant' => $tenant,
                    ], panel: 'app');
                }

                return null;
            })))
        )->shouldCache();
    }

    public function relativeUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->getKey(), fn () => optional(tenant(), fn (Tenant $tenant) => optional(Filament::getModelResource($this), function ($class) use ($tenant) {
                /** @var class-string<resource> $class */
                if ($class::hasPage('view')) {
                    return $class::getUrl('view', [
                        'record' => $this,
                        'tenant' => $tenant,
                    ], isAbsolute: false, panel: 'app');
                }

                /** @var class-string<resource> $class */
                if ($class::hasPage('edit')) {
                    return $class::getUrl('edit', [
                        'record' => $this,
                        'tenant' => $tenant,
                    ], isAbsolute: false, panel: 'app');
                }

                return null;
            })))
        )->shouldCache();
    }

    protected function initializeHasResourceUrlAttribute(): void
    {
        $this->appends = array_merge($this->appends, [
            'url',
            'relative_url',
        ]);
    }
}
