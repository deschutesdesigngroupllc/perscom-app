<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\GroupResource\RelationManagers;

use App\Filament\App\Resources\UnitResource;
use BackedEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static string|BackedEnum|null $icon = 'heroicon-o-rectangle-stack';

    public function form(Schema $schema): Schema
    {
        return UnitResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->description("The group's assigned units.")
            ->emptyStateDescription('Attach a unit to the group to get started.')
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->label('Add unit')
                    ->attachAnother(false)
                    ->multiple()
                    ->modalHeading('Add unit')
                    ->modalDescription('Assign a unit to the group.')
                    ->modalSubmitActionLabel('Add')
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make()
                    ->label('Remove unit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('units.order');
    }
}
