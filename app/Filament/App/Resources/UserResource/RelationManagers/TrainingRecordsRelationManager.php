<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\App\Resources\ServiceRecordResource;
use App\Filament\Tables\Columns\ExpiresAtColumn;
use App\Models\Document;
use App\Models\TrainingRecord;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class TrainingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'training_records';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedAcademicCap;

    protected static ?string $title = 'Training Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Training Records')
            ->description('The training records for the member.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->toggleable(false)
                    ->sortable(),
                ExpiresAtColumn::makeForCredentials()
                    ->placeholder(new HtmlString('&ndash;')),
                TextColumn::make('competencies.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->listWithLineBreaks(),
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
                            ->html(fn (TrainingRecord $record): ?string => $record->text),
                    ),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (TrainingRecord $record): ?Document => $record->document)
                            ->user(fn (TrainingRecord $record): ?User => $record->user)
                            ->attached(fn (TrainingRecord $record): TrainingRecord => $record),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New Training Record')
                    ->url(function (): string {
                        /** @var User $user */
                        $user = $this->getOwnerRecord();

                        return ServiceRecordResource::getUrl('create', [
                            'user_id' => $user->getKey(),
                        ]);
                    })
                    ->button(),
            ]);
    }
}
