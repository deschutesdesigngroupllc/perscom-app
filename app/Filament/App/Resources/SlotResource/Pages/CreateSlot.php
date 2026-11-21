<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SlotResource\Pages;

use App\Filament\App\Resources\SlotResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSlot extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = SlotResource::class;
}
