<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Models\AssignmentRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AssignmentRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignment_records';

    protected static ?string $icon = 'heroicon-o-rectangle-stack';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialty.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (?AssignmentRecord $record) => Color::hex($record->status->color ?? '#2563eb'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        Action::make('select')
                            ->visible(fn (?AssignmentRecord $record) => isset($record->document))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?AssignmentRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?AssignmentRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
                Tables\Columns\TextColumn::make('text')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable(),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New assignment record')
                    ->url(AssignmentRecordResource::getUrl('create'))
                    ->button(),
            ]);
    }
}
