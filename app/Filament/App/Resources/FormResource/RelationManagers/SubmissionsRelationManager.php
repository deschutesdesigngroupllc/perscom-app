<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\RelationManagers;

use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $icon = 'heroicon-o-folder-plus';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('created_at'),
                Infolists\Components\TextEntry::make('updated_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('The submissions for this form.')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ]);
    }
}
