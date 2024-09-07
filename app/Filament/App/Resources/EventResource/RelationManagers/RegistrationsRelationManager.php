<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $icon = 'heroicon-o-user-plus';

    protected $listeners = ['refreshRegistrations' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No registrations')
            ->emptyStateDescription('There are no users registered.')
            ->columns([
                Tables\Columns\TextColumn::make('registration.user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration.created_at')
                    ->label('Registered at')
                    ->dateTime(),
            ])
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
            ->defaultSort('events_registrations.created_at', 'desc');
    }
}
