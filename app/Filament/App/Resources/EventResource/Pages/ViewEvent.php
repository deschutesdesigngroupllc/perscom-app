<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Pages;

use App\Filament\App\Actions\Events\RsvpAction;
use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('url')
                ->url(fn (?Event $record) => $record->url)
                ->openUrlInNewTab()
                ->color('gray')
                ->label('Open'),
            RsvpAction::make(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
