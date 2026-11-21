<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CalendarResource\Pages;

use App\Filament\App\Resources\CalendarResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCalendar extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = CalendarResource::class;
}
