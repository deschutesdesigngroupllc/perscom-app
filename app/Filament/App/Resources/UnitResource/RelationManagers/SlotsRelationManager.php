<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UnitResource\RelationManagers;

use App\Filament\App\Resources\SlotResource;
use App\Models\Enums\RosterMode;
use App\Settings\DashboardSettings;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SlotsRelationManager extends RelationManager
{
    protected static string $relationship = 'slots';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        /** @var DashboardSettings $settings */
        $settings = app(DashboardSettings::class);

        return parent::canViewForRecord($ownerRecord, $pageClass)
            && $settings->roster_mode === RosterMode::MANUAL;
    }

    public function form(Schema $schema): Schema
    {
        return SlotResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->allowDuplicates()
            ->recordTitleAttribute('name')
            ->description('The available slots for the unit.')
            ->emptyStateDescription('Attach a slot to the unit to get started.')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->label('Add slot')
                    ->multiple()
                    ->modalHeading('Add slot')
                    ->modalDescription('Attach a slot to this unit.')
                    ->modalSubmitActionLabel('Add')
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remove slot'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('slots.order');
    }
}
