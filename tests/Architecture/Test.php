<?php

declare(strict_types=1);

arch('application does not use globals')
    ->expect(['dd', 'dump', 'ray'])
    ->not
    ->toBeUsed();
