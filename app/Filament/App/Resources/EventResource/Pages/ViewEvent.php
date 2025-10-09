<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Pages;

use App\Filament\App\Resources\EventResource;
use App\Filament\App\Resources\EventResource\Actions\RsvpAction;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('url')
                ->url(fn (?Event $record) => $record->url)
                ->openUrlInNewTab()
                ->color('gray')
                ->label('Open'),
            RsvpAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
