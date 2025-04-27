<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\TrainingRecordResource;
use App\Models\TrainingRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TrainingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'training_records';

    protected static ?string $icon = 'heroicon-o-academic-cap';

    public function table(Table $table): Table
    {
        return $table
            ->description('The training records for the user.')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('credentials.name')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('competencies.name')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('text')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        Action::make('select')
                            ->visible(fn (?TrainingRecord $record): bool => $record->document !== null)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?TrainingRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?TrainingRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New training record')
                    ->url(TrainingRecordResource::getUrl('create'))
                    ->button(),
            ]);
    }
}
