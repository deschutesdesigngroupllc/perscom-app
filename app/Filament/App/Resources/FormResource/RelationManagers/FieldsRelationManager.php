<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\RelationManagers;

use App\Filament\App\Resources\FieldResource;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedPencil;

    public function table(Table $table): Table
    {
        return FieldResource::relationTable($table);
    }
}
