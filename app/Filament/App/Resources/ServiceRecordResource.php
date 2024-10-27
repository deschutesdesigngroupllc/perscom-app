<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\ServiceRecordResource\Pages;
use App\Filament\App\Resources\ServiceRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\ServiceRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\ServiceRecordExporter;
use App\Livewire\App\ViewDocument;
use App\Models\ServiceRecord;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class ServiceRecordResource extends BaseResource
{
    protected static ?string $model = ServiceRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Record Information')
                    ->columns()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->required()
                            ->helperText('The user this record is assigned to.')
                            ->preload()
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->searchable()
                            ->columnSpanFull()
                            ->createOptionForm(fn ($form) => UserResource::form($form)),
                        Forms\Components\RichEditor::make('text')
                            ->required()
                            ->helperText('Information about the record.')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->timezone(UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            }))
                            ->columnSpanFull()
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('document_id')
                            ->helperText('The document for this record.')
                            ->preload()
                            ->relationship(name: 'document', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(fn ($form) => DocumentResource::form($form)),
                        Forms\Components\Select::make('author_id')
                            ->required()
                            ->default(Auth::user()->getAuthIdentifier())
                            ->helperText('The author of the record.')
                            ->preload()
                            ->relationship(name: 'author', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(fn ($form) => UserResource::form($form)),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Service Record')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name'),
                                Infolists\Components\TextEntry::make('text')
                                    ->html()
                                    ->prose()
                                    ->columnSpanFull(),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Infolists\Components\TextEntry::make('author.name'),
                                Infolists\Components\TextEntry::make('created_at'),
                                Infolists\Components\TextEntry::make('updated_at'),
                                Infolists\Components\TextEntry::make('deleted_at'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Document')
                            ->visible(fn (?ServiceRecord $record) => isset($record->document))
                            ->label(fn (?ServiceRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Infolists\Components\Livewire::make(ViewDocument::class, fn (?ServiceRecord $record) => [
                                    'document' => $record->document,
                                    'user' => $record->user,
                                    'model' => $record,
                                ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('select')
                            ->visible(fn (?ServiceRecord $record) => isset($record->document))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?ServiceRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?ServiceRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
                Tables\Columns\TextColumn::make('text')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable(),
            ])
            ->groups(['user.name', 'document.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(ServiceRecordExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttachmentsRelationManager::make(),
            CommentsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRecords::route('/'),
            'create' => Pages\CreateServiceRecord::route('/create'),
            'view' => Pages\ViewServiceRecord::route('/{record}'),
            'edit' => Pages\EditServiceRecord::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model|ServiceRecord $record): string
    {
        $user = optional($record->user)->name;

        return "$record->id: $user";
    }

    public static function getGlobalSearchResultDetails(Model|ServiceRecord $record): array
    {
        return [
            'Text' => $record->text,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['text', 'user.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }
}
