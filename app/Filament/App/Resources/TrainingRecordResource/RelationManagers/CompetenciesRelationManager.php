<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TrainingRecordResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CompetenciesRelationManager extends RelationManager
{
    protected static string $relationship = 'competencies';

    protected static ?string $icon = 'heroicon-o-numbered-list';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('training_records')
            ->description('The competencies that were covered.')
            ->emptyStateDescription('There are no competencies assigned to this training record.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->html()
                    ->wrap(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->listWithLineBreaks(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->label('Add competency')
                    ->modalHeading('Add competency')
                    ->modalDescription('Select a competency from the list below to attach to this training record.'),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove competency')
                    ->modalHeading('Remove competency'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
