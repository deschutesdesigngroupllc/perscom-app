<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

use function sprintf;

/**
 * @mixin Eloquent
 */
trait HasResourceLabel
{
    public function getLabel(): string
    {
        $resource = Str::of(class_basename($this))
            ->split('/(?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z])/', -1, PREG_SPLIT_NO_EMPTY)
            ->implode(' ');

        return sprintf('%s: %s', $resource, $this->getKey());
    }

    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->getLabel()
        )->shouldCache();
    }

    protected function initializeHasResourceLabel(): void
    {
        $this->appends = array_merge($this->appends, [
            'label',
        ]);
    }
}
