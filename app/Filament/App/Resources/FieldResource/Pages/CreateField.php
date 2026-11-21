<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FieldResource\Pages;

use App\Filament\App\Resources\FieldResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateField extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = FieldResource::class;
}
