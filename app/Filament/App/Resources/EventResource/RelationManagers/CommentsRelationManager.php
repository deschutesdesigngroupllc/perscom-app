<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\RelationManagers;

use App\Filament\App\Resources\CommentResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $icon = 'heroicon-o-chat-bubble-left';

    public function form(Form $form): Form
    {
        return CommentResource::form($form);
    }

    public function table(Table $table): Table
    {
        return CommentResource::relationManagerTable($table);
    }
}
