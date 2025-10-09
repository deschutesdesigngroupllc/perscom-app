<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\AwardRecordResource;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Models\AwardRecord;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AwardRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'award_records';

    protected static string|BackedEnum|null $icon = 'heroicon-o-trophy';

    protected static ?string $title = 'Award Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Award Records')
            ->description('The award records for the user.')
            ->columns([
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('award.name')
                    ->sortable(),
                ImageColumn::make('award.image.path')
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
                            ->html(fn (AwardRecord $record) => $record->text),
                    ),
                TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (AwardRecord $record) => $record->document)
                            ->user(fn (AwardRecord $record) => $record->user)
                            ->attached(fn (AwardRecord $record): AwardRecord => $record),
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
