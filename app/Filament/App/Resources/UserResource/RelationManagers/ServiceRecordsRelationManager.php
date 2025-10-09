<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\RelationManagers;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\App\Resources\ServiceRecordResource;
use App\Models\ServiceRecord;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'service_records';

    protected static string|BackedEnum|null $icon = 'heroicon-o-clipboard-document-list';

    protected static ?string $title = 'Service Records';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Service Records')
            ->description('The service records for the user.')
            ->columns([
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
                TextColumn::make('text')
                    ->icon('heroicon-o-document')
                    ->wrap(false)
                    ->formatStateUsing(fn ($state) => Str::limit($state, 20))
                    ->html()
                    ->sortable()
                    ->action(
                        ViewHtmlAction::make()
                            ->modalHeading('Text')
                            ->html(fn (ServiceRecord $record) => $record->text),
                    ),
                TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (ServiceRecord $record) => $record->document)
                            ->user(fn (ServiceRecord $record) => $record->user)
                            ->attached(fn (ServiceRecord $record): ServiceRecord => $record),
                    ),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('New service record')
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
