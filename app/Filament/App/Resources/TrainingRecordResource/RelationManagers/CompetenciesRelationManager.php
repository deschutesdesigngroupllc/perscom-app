<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TrainingRecordResource\RelationManagers;

use BackedEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompetenciesRelationManager extends RelationManager
{
    protected static string $relationship = 'competencies';

    protected static string|BackedEnum|null $icon = 'heroicon-o-numbered-list';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('training_records')
            ->description('The competencies that were covered.')
            ->emptyStateDescription('There are no competencies assigned to this training record.')
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->sortable()
                    ->html()
                    ->wrap(),
                TextColumn::make('categories.name')
                    ->listWithLineBreaks(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->label('Add competency')
                    ->modalHeading('Add competency')
                    ->modalDescription('Select a competency from the list below to attach to this training record.'),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remove competency')
                    ->modalHeading('Remove competency'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
