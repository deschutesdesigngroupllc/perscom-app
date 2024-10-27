<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\AwardRecordResource;
use App\Models\AwardRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AwardRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'award_records';

    protected static ?string $icon = 'heroicon-o-trophy';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('award.name')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('award.image.path')
                    ->disk('s3')
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
                            ->visible(fn (?AwardRecord $record) => isset($record->document))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?AwardRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?AwardRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New award record')
                    ->url(AwardRecordResource::getUrl('create'))
                    ->button(),
            ]);
    }
}
