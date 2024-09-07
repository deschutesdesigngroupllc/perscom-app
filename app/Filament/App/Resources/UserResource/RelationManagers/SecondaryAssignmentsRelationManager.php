<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Models\AssignmentRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class SecondaryAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'secondary_assignment_records';

    protected static ?string $icon = 'heroicon-o-calendar-days';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('position.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialty.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (?AssignmentRecord $record) => Color::hex($record->status->color ?? '#2563eb'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->heading(null)
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No secondary assignments found')
            ->emptyStateActions([
                Action::make('create')
                    ->label('New secondary assignment record')
                    ->url(AssignmentRecordResource::getUrl('create'))
                    ->button(),
            ])
            ->paginated(false);
    }
}
