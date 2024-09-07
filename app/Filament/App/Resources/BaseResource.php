<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    protected static bool $isScopedToTenant = false;
}
