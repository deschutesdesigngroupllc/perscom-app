<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\Actions\PromoteMemberAction;
use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Models\RankRecord;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class RankRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'rank_records';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedChevronDoubleUp;

    protected static ?string $title = 'Rank Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Rank Records')
            ->description('The rank records for the member.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('rank.name')
                    ->sortable(),
                ImageColumn::make('rank.image.path')
                    ->placeholder('No Image')
                    ->label(''),
                TextColumn::make('text')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
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
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (RankRecord $record) => $record->document)
                            ->user(fn (RankRecord $record) => $record->user)
                            ->attached(fn (RankRecord $record): RankRecord => $record),
                    ),
            ])
            ->headerActions([
                PromoteMemberAction::make(),
            ]);
    }
}
