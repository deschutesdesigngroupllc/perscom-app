<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\Actions\AssignMemberAction;
use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Models\AssignmentRecord;
use App\Support\Brand;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AssignmentRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignment_records';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedRectangleStack;

    protected static ?string $title = 'Assignment Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Assignment Records')
            ->description('The assignment records for the member.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('position.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->sortable(),
                TextColumn::make('specialty.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->sortable(),
                TextColumn::make('status.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->badge()
                    ->color(fn (?AssignmentRecord $record): array => Color::generateV3Palette($record->status->color ?? Brand::NEUTRAL_HEX))
                    ->sortable(),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (AssignmentRecord $record) => $record->document)
                            ->user(fn (AssignmentRecord $record) => $record->user)
                            ->attached(fn (AssignmentRecord $record): AssignmentRecord => $record),
                    ),
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
                            ->html(fn (AssignmentRecord $record) => $record->text),
                    ),
            ])
            ->headerActions([
                AssignMemberAction::make(),
            ]);
    }
}
