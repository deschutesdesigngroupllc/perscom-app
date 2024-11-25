<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\RelationManagers;

use App\Filament\App\Resources\FieldResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $icon = 'heroicon-o-pencil';

    public function table(Table $table): Table
    {
        return FieldResource::relationTable($table);
    }
}
