<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\QualificationRecordResource;
use App\Models\QualificationRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class QualificationRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'qualification_records';

    protected static ?string $icon = 'heroicon-o-star';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qualification.name')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('qualification.image.path')
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
                            ->visible(fn (?QualificationRecord $record) => isset($record->document))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?QualificationRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?QualificationRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New qualification record')
                    ->url(QualificationRecordResource::getUrl('create'))
                    ->button(),
            ]);
    }
}
