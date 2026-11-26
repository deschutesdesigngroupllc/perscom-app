<?php

declare(strict_types=1);

namespace App\Traits;

use App\Support\Brand;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @property-read string $color
 */
trait HasColorField
{
    public function getColor(): string|array|null
    {
        return $this->color;
    }

    /**
     * Get the color, falling back to the brand's primary color.
     *
     * @return Attribute<string, never>
     */
    protected function color(): Attribute
    {
        return Attribute::get(fn ($value): string => $value ?? Brand::PRIMARY_HEX);
    }

    protected function initializeHasColorField(): void
    {
        $this->mergeFillable([
            'color',
        ]);
    }
}
