<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankRecordResource\RelationManagers;

use App\Filament\App\Resources\AttachmentResource;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedPaperClip;

    public function form(Schema $schema): Schema
    {
        return AttachmentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AttachmentResource::relationManagerTable($table);
    }
}
