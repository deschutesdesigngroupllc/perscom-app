<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;

/**
 * @mixin Eloquent
 */
trait HasIcon
{
    protected function initializeHasIcon(): void
    {
        $this->mergeFillable([
            'icon',
        ]);
    }
}
