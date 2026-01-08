<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankRecordResource\Pages;

use App\Filament\App\Resources\RankRecordResource;
use App\Livewire\Filament\App\ViewDocument;
use App\Models\RankRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;

class ViewRankRecord extends ViewRecord
{
    protected static string $resource = RankRecordResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Document')
                ->color('gray')
                ->slideOver()
                ->modalHeading(fn (RankRecord $record): string => $record->document->name)
                ->visible(fn (RankRecord $record): bool => filled($record->document))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    Livewire::make(ViewDocument::class, fn (?RankRecord $record): array => [
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
