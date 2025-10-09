<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\App\Resources\RankRecordResource;
use App\Models\RankRecord;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RankRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'rank_records';

    protected static string|BackedEnum|null $icon = 'heroicon-o-chevron-double-up';

    protected static ?string $title = 'Rank Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Rank Records')
            ->description('The rank records for the user.')
            ->columns([
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('rank.name')
                    ->sortable(),
                ImageColumn::make('rank.image.path')
                    ->label(''),
                TextColumn::make('text')
                    ->icon('heroicon-o-document')
                    ->wrap(false)
                    ->formatStateUsing(fn ($state) => Str::limit($state, 20))
                    ->html()
                    ->sortable()
                    ->action(
                        ViewHtmlAction::make()
                            ->modalHeading('Text')
                            ->html(fn (RankRecord $record) => $record->text),
                    ),
                TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (RankRecord $record) => $record->document)
                            ->user(fn (RankRecord $record) => $record->user)
                            ->attached(fn (RankRecord $record): RankRecord => $record),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New rank record')
                    ->url(function (): string {
                        /** @var User $user */
                        $user = $this->getOwnerRecord();

                        return RankRecordResource::getUrl('create', [
                            'user_id' => $user->getKey(),
                        ]);
                    })
                    ->button(),
            ]);
    }
}
