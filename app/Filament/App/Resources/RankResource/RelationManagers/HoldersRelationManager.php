<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankResource\RelationManagers;

use App\Filament\App\Resources\UserResource;
use App\Models\User;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HoldersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Holders';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedUsers;

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('Members who currently hold this rank.')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['unit', 'position']))
            ->columns([
                TextColumn::make('name')
                    ->label('Member')
                    ->url(fn (User $record): string => UserResource::getUrl('view', ['record' => $record]))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position.name')
                    ->label('Position')
                    ->placeholder('No Position'),
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->placeholder('No Unit'),
            ])
            ->defaultSort('name')
            ->emptyStateIcon(Heroicon::OutlinedUsers)
            ->emptyStateHeading('No One Holds This Rank')
            ->emptyStateDescription('Promote a member from their profile or the Promotion Board.');
    }
}
