<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UnitResource\RelationManagers;

use App\Filament\App\Resources\SlotResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SlotsRelationManager extends RelationManager
{
    protected static string $relationship = 'slots';

    public function form(Form $form): Form
    {
        return SlotResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->allowDuplicates()
            ->recordTitleAttribute('name')
            ->description('The available slots for the unit.')
            ->emptyStateDescription('Attach a slot to the unit to get started.')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->label('Add slot')
                    ->multiple()
                    ->modalHeading('Add slot')
                    ->modalDescription('Attach a slot to this unit.')
                    ->modalSubmitActionLabel('Add')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove slot'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('slots.order');
    }
}
