<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\CombatRecordResource;
use App\Models\CombatRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CombatRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'combat_records';

    protected static ?string $icon = 'heroicon-o-fire';

    public function table(Table $table): Table
    {
        return $table
            ->description('The combat records for the user.')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('text')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        Action::make('select')
                            ->visible(fn (?CombatRecord $record): bool => $record->document !== null)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?CombatRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?CombatRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New combat record')
                    ->url(CombatRecordResource::getUrl('create'))
                    ->button(),
            ]);
    }
}
