<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static string|BackedEnum|null $icon = 'heroicon-o-folder-plus';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at'),
                TextEntry::make('updated_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('The forms the user has submitted.')
            ->columns([
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ]);
    }
}
