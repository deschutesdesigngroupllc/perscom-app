<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UnitResource\Pages;

use App\Filament\App\Resources\UnitResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateUnit extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = UnitResource::class;
}
