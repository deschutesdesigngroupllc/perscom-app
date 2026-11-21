<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Categories\Pages;

use App\Filament\App\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCategory extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = CategoryResource::class;
}
