<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\GroupResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static ?string $icon = 'heroicon-o-rectangle-stack';

    public function table(Table $table): Table
    {
        return $table
            ->description('The group\'s assigned units.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Add unit')
                    ->attachAnother(false)
                    ->multiple()
                    ->modalDescription('Add unit')
                    ->modalSubmitActionLabel('Add')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove unit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->reorderable('units_groups.order')
            ->defaultSort('units_groups.order')
            ->recordTitleAttribute('name');
    }
}
