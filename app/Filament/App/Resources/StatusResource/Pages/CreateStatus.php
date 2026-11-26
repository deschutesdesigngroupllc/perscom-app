<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StatusResource\Pages;

use App\Filament\App\Resources\StatusResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateStatus extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = StatusResource::class;
}
