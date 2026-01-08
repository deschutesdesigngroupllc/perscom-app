<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\RelationManagers;

use App\Models\Status;
use BackedEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'statuses';

    protected static string|BackedEnum|null $icon = 'heroicon-o-scale';

    public function table(Table $table): Table
    {
        return $table
            ->description('The status history for this submission.')
            ->recordTitleAttribute('name')
            ->description('The status history of this form submission.')
            ->columns([
                TextColumn::make('name')
                    ->toggleable(false)
                    ->badge()
                    ->color(fn (?Status $record): array => Color::generateV3Palette($record->color ?? '#2563eb')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Set status')
                    ->modalHeading('Set Status')
                    ->modalDescription('Set the current status of the submission.')
                    ->modalSubmitActionLabel('Save')
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('model_has_statuses.created_at', 'desc');
    }
}
