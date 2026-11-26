<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\RelationManagers;

use App\Models\Status;
use App\Support\Brand;
use BackedEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'statuses';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedScale;

    public function table(Table $table): Table
    {
        return $table
            ->description('The status history of this form submission.')
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->toggleable(false)
                    ->badge()
                    ->color(fn (?Status $record): array => Color::generateV3Palette($record->color ?? Brand::NEUTRAL_HEX)),
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
                DetachAction::make()
                    ->label('Remove'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Remove selected'),
                ]),
            ])
            ->defaultSort('model_has_statuses.created_at', 'desc');
    }
}
