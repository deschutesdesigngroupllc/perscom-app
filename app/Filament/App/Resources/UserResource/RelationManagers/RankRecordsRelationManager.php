<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\RankRecordResource;
use App\Models\RankRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RankRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'rank_records';

    protected static ?string $icon = 'heroicon-o-chevron-double-up';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('rank.name')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('rank.image.path')
                    ->label(''),
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
                            ->visible(fn (?RankRecord $record): bool => $record->document !== null)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?RankRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?RankRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New rank record')
                    ->url(RankRecordResource::getUrl('create'))
                    ->button(),
            ]);
    }
}
