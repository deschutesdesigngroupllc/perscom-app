<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\App\Resources\QualificationRecordResource;
use App\Models\QualificationRecord;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class QualificationRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'qualification_records';

    protected static string|BackedEnum|null $icon = 'heroicon-o-star';

    protected static ?string $title = 'Qualification Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Qualification Records')
            ->description('The qualification records for the user.')
            ->columns([
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('qualification.name')
                    ->sortable(),
                ImageColumn::make('qualification.image.path')
                    ->placeholder('No Image')
                    ->label(''),
                TextColumn::make('text')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon('heroicon-o-document')
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
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (QualificationRecord $record) => $record->document)
                            ->user(fn (QualificationRecord $record) => $record->user)
                            ->attached(fn (QualificationRecord $record): QualificationRecord => $record),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New qualification record')
                    ->url(function (): string {
                        /** @var User $user */
                        $user = $this->getOwnerRecord();

                        return QualificationRecordResource::getUrl('create', [
                            'user_id' => $user->getKey(),
                        ]);
                    })
                    ->button(),
            ]);
    }
}
