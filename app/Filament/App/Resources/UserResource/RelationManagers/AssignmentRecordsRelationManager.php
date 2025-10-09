<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\AssignmentRecordResource;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AssignmentRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignment_records';

    protected static string|BackedEnum|null $icon = 'heroicon-o-rectangle-stack';

    protected static ?string $title = 'Assignment Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Assignment Records')
            ->description('The assignment records for the user.')
            ->columns([
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
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
                TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (AssignmentRecord $record) => $record->document)
                            ->user(fn (AssignmentRecord $record) => $record->user)
                            ->attached(fn (AssignmentRecord $record): AssignmentRecord => $record),
                    ),
                TextColumn::make('text')
                    ->icon('heroicon-o-document')
                    ->wrap(false)
                    ->formatStateUsing(fn ($state) => Str::limit($state, 20))
                    ->html()
                    ->sortable()
                    ->action(
                        ViewHtmlAction::make()
                            ->modalHeading('Text')
                            ->html(fn (AssignmentRecord $record) => $record->text),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New assignment record')
                    ->url(function (): string {
                        /** @var User $user */
                        $user = $this->getOwnerRecord();

                        return AssignmentRecordResource::getUrl('create', [
                            'user_id' => $user->getKey(),
                            'type' => AssignmentRecordType::PRIMARY,
                        ]);
                    })
                    ->button(),
            ]);
    }
}
