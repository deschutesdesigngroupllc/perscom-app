<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AlertResource\Pages;

use App\Filament\Admin\Resources\AlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlerts extends ListRecords
{
    protected static string $resource = AlertResource::class;

    protected ?string $subheading = 'Manage alerts to notify users of important updates or changes.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
