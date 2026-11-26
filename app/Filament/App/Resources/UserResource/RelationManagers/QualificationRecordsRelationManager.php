<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\Actions\QualifyMemberAction;
use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\Tables\Columns\ExpiresAtColumn;
use App\Models\QualificationRecord;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class QualificationRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'qualification_records';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedStar;

    protected static ?string $title = 'Qualification Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Qualification Records')
            ->description('The qualification records for the member.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('qualification.name')
                    ->sortable(),
                ExpiresAtColumn::make()
                    ->sortable(),
                ImageColumn::make('qualification.image.path')
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
                            ->html(fn (QualificationRecord $record) => $record->text),
                    ),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (QualificationRecord $record) => $record->document)
                            ->user(fn (QualificationRecord $record) => $record->user)
                            ->attached(fn (QualificationRecord $record): QualificationRecord => $record),
                    ),
            ])
            ->headerActions([
                QualifyMemberAction::make(),
            ]);
    }
}
