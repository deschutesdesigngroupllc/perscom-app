<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @mixin Eloquent
 *
 * @property-read string $color
 */
trait HasColorField
{
    public function color(): Attribute
    {
        return Attribute::get(fn ($value) => $value ?? '#2563eb');
    }

    public function getColor(): string|array|null
    {
        return $this->color;
    }

    protected function initializeHasColorField(): void
    {
        $this->mergeFillable([
            'color',
        ]);
    }
}
