<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\RelationManagers;

use App\Filament\App\Resources\AttachmentResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $icon = 'heroicon-o-paper-clip';

    public function form(Form $form): Form
    {
        return AttachmentResource::form($form);
    }

    public function table(Table $table): Table
    {
        return AttachmentResource::relationManagerTable($table);
    }
}
