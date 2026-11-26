<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationResource\RelationManagers;

use App\Filament\App\Resources\UserResource;
use App\Filament\Tables\Columns\ExpiresAtColumn;
use App\Models\QualificationRecord;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HoldersRelationManager extends RelationManager
{
    protected static string $relationship = 'qualification_records';

    protected static ?string $title = 'Holders';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedUsers;

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('Members who hold this qualification.')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['user.rank', 'user.unit']))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Member')
                    ->description(fn (QualificationRecord $record): ?string => $record->user?->unit?->name)
                    ->url(fn (QualificationRecord $record): ?string => $record->user ? UserResource::getUrl('view', ['record' => $record->user]) : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.rank.name')
                    ->label('Rank')
                    ->badge()
                    ->color('brass')
                    ->placeholder('No Rank'),
                ExpiresAtColumn::make('expires_at'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->toggleable(false)
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon(Heroicon::OutlinedUsers)
            ->emptyStateHeading('No One Holds This Qualification Yet')
            ->emptyStateDescription("Add a qualification record from a member's profile to see them here.");
    }
}
