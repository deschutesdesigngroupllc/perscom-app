<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Pages;

use App\Filament\App\Resources\ServiceRecordResource;
use App\Livewire\Filament\App\ViewDocument;
use App\Models\ServiceRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;

class ViewServiceRecord extends ViewRecord
{
    protected static string $resource = ServiceRecordResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Document')
                ->color('gray')
                ->slideOver()
                ->modalHeading(fn (ServiceRecord $record): string => $record->document->name)
                ->visible(fn (ServiceRecord $record): bool => filled($record->document))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    Livewire::make(ViewDocument::class, fn (?ServiceRecord $record): array => [
                        'document' => $record->document,
                        'user' => $record->user,
                        'model' => $record,
                    ]),
                ]),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
