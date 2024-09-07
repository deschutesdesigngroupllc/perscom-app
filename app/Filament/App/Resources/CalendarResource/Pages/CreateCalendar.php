<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CalendarResource\Pages;

use App\Filament\App\Resources\CalendarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCalendar extends CreateRecord
{
    protected static string $resource = CalendarResource::class;
}
