<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ImageResource\Pages;

use App\Filament\App\Resources\ImageResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateImage extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = ImageResource::class;
}
