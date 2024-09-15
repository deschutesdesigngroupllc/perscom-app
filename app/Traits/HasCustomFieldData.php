<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;
use Stancl\VirtualColumn\VirtualColumn;

/**
 * @mixin Eloquent
 */
trait HasCustomFieldData
{
    use VirtualColumn;

    protected function initializeHasCustomFieldData(): void
    {
        $this->guard([]);
        $this->setHidden(array_merge($this->getHidden(), [
            'data',
        ]));
    }
}
