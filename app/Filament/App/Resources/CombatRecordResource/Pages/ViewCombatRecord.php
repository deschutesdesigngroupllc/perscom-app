<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use App\Livewire\Filament\App\ViewDocument;
use App\Models\CombatRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;

class ViewCombatRecord extends ViewRecord
{
    protected static string $resource = CombatRecordResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Document')
                ->color('gray')
                ->slideOver()
                ->modalHeading(fn (CombatRecord $record): string => $record->document->name)
                ->visible(fn (CombatRecord $record) => filled($record->document))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    Livewire::make(ViewDocument::class, fn (?CombatRecord $record): array => [
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
