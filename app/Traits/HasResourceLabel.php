<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @mixin Eloquent
 */
trait HasResourceLabel
{
    public function label(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->getLabel()
        )->shouldCache();
    }

    public function getLabel(): string
    {
        $resource = implode(' ', preg_split('/(?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z])/', class_basename($this), -1, PREG_SPLIT_NO_EMPTY));

        return "$resource: {$this->getKey()}";
    }

    protected function initializeHasResourceLabel(): void
    {
        $this->appends = array_merge($this->appends, [
            'label',
        ]);
    }
}
