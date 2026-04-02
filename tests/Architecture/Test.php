<?php

declare(strict_types=1);

/** @phpstan-ignore method.notFound */
arch('application does not use globals')
    ->expect(['dd', 'dump', 'ray'])
    ->not
    ->toBeUsed();
