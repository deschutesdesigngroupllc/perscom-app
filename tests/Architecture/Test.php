<?php

declare(strict_types=1);

arch('application does not use globals')
    /** @phpstan-ignore method.notFound */
    ->expect(['dd', 'dump', 'ray'])
    ->not
    ->toBeUsed();
