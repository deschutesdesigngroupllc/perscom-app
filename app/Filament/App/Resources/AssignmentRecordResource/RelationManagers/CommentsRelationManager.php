<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\RelationManagers;

use App\Filament\App\Resources\CommentResource;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static string|BackedEnum|null $icon = 'heroicon-o-chat-bubble-left';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return CommentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CommentResource::relationManagerTable($table);
    }
}
