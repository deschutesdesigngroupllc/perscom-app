<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\CombatRecordResource;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Models\CombatRecord;
use App\Models\Document;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class CombatRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'combat_records';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedFire;

    protected static ?string $title = 'Combat Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Combat Records')
            ->description('The combat records for the member.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->toggleable(false)
                    ->sortable(),
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
                            ->html(fn (CombatRecord $record): ?string => $record->text),
                    ),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon(Heroicon::OutlinedDocument)
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (CombatRecord $record): ?Document => $record->document)
                            ->user(fn (CombatRecord $record): ?User => $record->user)
                            ->attached(fn (CombatRecord $record): CombatRecord => $record),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New Combat Record')
                    ->url(function (): string {
                        /** @var User $user */
                        $user = $this->getOwnerRecord();

                        return CombatRecordResource::getUrl('create', [
                            'user_id' => $user->getKey(),
                        ]);
                    })
                    ->button(),
            ]);
    }
}
