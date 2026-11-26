<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\Actions\AwardMemberAction;
use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Models\AwardRecord;
use App\Models\Document;
use App\Models\User;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AwardRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'award_records';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedTrophy;

    protected static ?string $title = 'Award Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Award Records')
            ->description('The award records for the member.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('award.name')
                    ->sortable(),
                ImageColumn::make('award.image.path')
                    ->placeholder('No Image')
                    ->label(''),
                TextColumn::make('text')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
                    ->wrap(false)
                    ->formatStateUsing(fn (?string $state): string => Str::limit($state, 20))
                    ->html()
                    ->sortable()
                    ->action(
                        ViewHtmlAction::make()
                            ->modalHeading('Text')
                            ->html(fn (AwardRecord $record): ?string => $record->text),
                    ),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (AwardRecord $record): ?Document => $record->document)
                            ->user(fn (AwardRecord $record): ?User => $record->user)
                            ->attached(fn (AwardRecord $record): AwardRecord => $record),
                    ),
            ])
            ->headerActions([
                AwardMemberAction::make(),
            ]);
    }
}
