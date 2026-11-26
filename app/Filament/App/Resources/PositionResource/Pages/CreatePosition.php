<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PositionResource\Pages;

use App\Filament\App\Resources\PositionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreatePosition extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = PositionResource::class;
}
