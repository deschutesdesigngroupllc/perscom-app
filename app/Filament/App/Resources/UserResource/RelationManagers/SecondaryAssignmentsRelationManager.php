<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SecondaryAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'secondary_assignment_records';

    protected static string|BackedEnum|null $icon = 'heroicon-o-calendar-days';

    protected static ?string $title = 'Training Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Secondary Assignment(s)')
            ->description('The user\'s secondary assignments.')
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('position.name')
                    ->sortable(),
                TextColumn::make('specialty.name')
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->sortable(),
                TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (?AssignmentRecord $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No secondary assignments found')
            ->emptyStateActions([
                Action::make('create')
                    ->label('New secondary assignment record')
                    ->url(function (): string {
                        /** @var User $user */
                        $user = $this->getOwnerRecord();

                        return AssignmentRecordResource::getUrl('create', [
                            'user_id' => $user->getKey(),
                            'type' => AssignmentRecordType::SECONDARY,
                        ]);
                    })
                    ->button(),
            ])
            ->paginated(false);
    }
}
