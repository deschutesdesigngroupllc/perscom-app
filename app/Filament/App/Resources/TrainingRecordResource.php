<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TrainingRecordResource\Pages\CreateTrainingRecord;
use App\Filament\App\Resources\TrainingRecordResource\Pages\EditTrainingRecord;
use App\Filament\App\Resources\TrainingRecordResource\Pages\ListTrainingRecords;
use App\Filament\App\Resources\TrainingRecordResource\Pages\ViewTrainingRecord;
use App\Filament\App\Resources\TrainingRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\TrainingRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\TrainingRecordResource\RelationManagers\CompetenciesRelationManager;
use App\Filament\Exports\TrainingRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Livewire\App\ViewDocument;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Settings\NotificationSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class TrainingRecordResource extends BaseResource
{
    protected static ?string $model = TrainingRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|UnitEnum|null $navigationGroup = 'Training';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Details')
                            ->columns()
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Select::make('user_id')
                                    ->label(fn ($operation): string => $operation === 'create' ? 'User(s)' : 'User')
                                    ->multiple(fn ($operation): bool => $operation === 'create')
                                    ->required()
                                    ->helperText('The user this record is assigned to.')
                                    ->preload()
                                    ->options(fn () => User::orderBy('name')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => UserResource::form($form)),
                                Select::make('instructor_id')
                                    ->required()
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->helperText('The instructor of the training.')
                                    ->preload()
                                    ->relationship(name: 'instructor', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => UserResource::form($form)),
                                Select::make('credentials.name')
                                    ->columnSpanFull()
                                    ->required()
                                    ->helperText('The credentials that were earned.')
                                    ->preload()
                                    ->relationship(name: 'credentials', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => CredentialResource::form($form)),
                                RichEditor::make('text')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('Information about the record.')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                DateTimePicker::make('created_at')
                                    ->columnSpanFull()
                                    ->default(now())
                                    ->required(),
                                Select::make('document_id')
                                    ->helperText('The document for this record.')
                                    ->preload()
                                    ->relationship(name: 'document', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => DocumentResource::form($form)),
                                Select::make('author_id')
                                    ->required()
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->helperText('The author of the record.')
                                    ->preload()
                                    ->relationship(name: 'author', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => UserResource::form($form)),
                            ]),
                        Tab::make('Notifications')
                            ->visible(fn ($operation): bool => $operation === 'create')
                            ->icon('heroicon-o-bell')
                            ->schema(function (): array {
                                /** @var NotificationSettings $settings */
                                $settings = app(NotificationSettings::class);

                                return [
                                    ModelNotification::make(
                                        alert: new HtmlString("<div class='font-bold'>The recipients will already receive a notification about the new record.</div>"),
                                        defaults: data_get($settings->toArray(), 'training_records'),
                                    ),
                                ];
                            }),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Training Record')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('instructor.name'),
                                TextEntry::make('credentials.name')
                                    ->listWithLineBreaks(),
                                TextEntry::make('text')
                                    ->html()
                                    ->prose()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('author.name'),
                                TextEntry::make('created_at'),
                                TextEntry::make('updated_at'),
                            ]),
                        Tab::make('Document')
                            ->visible(fn (?TrainingRecord $record): bool => $record->document !== null)
                            ->label(fn (?TrainingRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Livewire::make(ViewDocument::class, fn (?TrainingRecord $record): array => [
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
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('credentials.name')
                    ->listWithLineBreaks(),
                TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Action::make('select')
                            ->visible(fn (?TrainingRecord $record): bool => $record->document !== null)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?TrainingRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?TrainingRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
                TextColumn::make('text')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['credentials.name', 'document.name', 'user.name'])
            ->filters([
                SelectFilter::make('credentials')
                    ->relationship('credentials', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(TrainingRecordExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttachmentsRelationManager::class,
            CommentsRelationManager::class,
            CompetenciesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingRecords::route('/'),
            'create' => CreateTrainingRecord::route('/create'),
            'view' => ViewTrainingRecord::route('/{record}'),
            'edit' => EditTrainingRecord::route('/{record}/edit'),
        ];
    }

    /**
     * @param  TrainingRecord  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $user = optional($record->user)->name;

        return "$record->id: $user";
    }

    /**
     * @param  TrainingRecord  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (blank($record->text)) {
            return [];
        }

        return [
            'Text' => Str::of($record->text)->stripTags()->limit()->squish()->toString(),
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
