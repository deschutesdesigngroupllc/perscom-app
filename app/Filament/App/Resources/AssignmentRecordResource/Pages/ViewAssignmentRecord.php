<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Livewire\Filament\App\ViewDocument;
use App\Models\AssignmentRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;

class ViewAssignmentRecord extends ViewRecord
{
    protected static string $resource = AssignmentRecordResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Document')
                ->color('gray')
                ->slideOver()
                ->modalHeading(fn (AssignmentRecord $record): string => $record->document->name)
                ->visible(fn (AssignmentRecord $record) => filled($record->document))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    Livewire::make(ViewDocument::class, fn (?AssignmentRecord $record): array => [
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
