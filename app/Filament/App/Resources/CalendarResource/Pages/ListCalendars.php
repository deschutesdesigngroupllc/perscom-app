<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CalendarResource\Pages;

use App\Filament\App\Resources\CalendarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalendars extends ListRecords
{
    protected static string $resource = CalendarResource::class;

    protected ?string $subheading = 'Manage your system-wide calendars.';

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
