<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Resources\AwardRecordResource;
use App\Models\AwardRecord;
use App\Models\User;
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
            ->description('The award records for the user.')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('award.name')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('award.image.path')
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
                            ->visible(fn (?AwardRecord $record): bool => $record->document !== null)
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
                    ->url(function (): string {
                        /** @var User $user */
                        $user = $this->getOwnerRecord();

                        return AwardRecordResource::getUrl('create', [
                            'user_id' => $user->getKey(),
                        ]);
                    })
                    ->button(),
            ]);
    }
}
