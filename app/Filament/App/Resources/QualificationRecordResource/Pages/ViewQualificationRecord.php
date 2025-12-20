<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationRecordResource\Pages;

use App\Filament\App\Resources\QualificationRecordResource;
use App\Livewire\Filament\App\ViewDocument;
use App\Models\QualificationRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;

class ViewQualificationRecord extends ViewRecord
{
    protected static string $resource = QualificationRecordResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Document')
                ->color('gray')
                ->slideOver()
                ->modalHeading(fn (QualificationRecord $record): string => $record->document->name)
                ->visible(fn (QualificationRecord $record) => filled($record->document))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    Livewire::make(ViewDocument::class, fn (?QualificationRecord $record): array => [
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
