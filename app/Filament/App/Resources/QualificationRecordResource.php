<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\App\Resources\QualificationRecordResource\Pages\CreateQualificationRecord;
use App\Filament\App\Resources\QualificationRecordResource\Pages\EditQualificationRecord;
use App\Filament\App\Resources\QualificationRecordResource\Pages\ListQualificationRecords;
use App\Filament\App\Resources\QualificationRecordResource\Pages\ViewQualificationRecord;
use App\Filament\App\Resources\QualificationRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\QualificationRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\QualificationRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Livewire\Filament\App\ViewDocument;
use App\Models\QualificationRecord;
use App\Models\User;
use App\Settings\NotificationSettings;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class QualificationRecordResource extends BaseResource
{
    protected static ?string $model = QualificationRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected static ?int $navigationSort = 7;

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
                                    ->createOptionForm(fn (Schema $form): Schema => UserResource::form($form)),
                                Select::make('qualification_id')
                                    ->required()
                                    ->helperText('The qualification for this record.')
                                    ->preload()
                                    ->relationship(name: 'qualification', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => QualificationResource::form($form)),
                                RichEditor::make('text')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('Optional information about the record.')
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
                                    ->createOptionForm(fn (Schema $form): Schema => DocumentResource::form($form)),
                                Select::make('author_id')
                                    ->required()
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->helperText('The author of the record.')
                                    ->preload()
                                    ->relationship(name: 'author', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UserResource::form($form)),
                            ]),
                        Tab::make('Notifications')
                            ->visible(fn ($operation): bool => $operation === 'create')
                            ->icon('heroicon-o-bell')
                            ->schema(function (): array {
                                /** @var NotificationSettings $settings */
                                $settings = app(NotificationSettings::class);

                                return [
                                    ModelNotification::make(
                                        alert: new HtmlString("<div class='font-bold max-w-2xl'>The recipients will already receive a notification about the new record. Default notification configuration can be set in your system settings.</div>"),
                                        defaults: data_get($settings->toArray(), 'qualification_records'),
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
                        Tab::make('Qualification Record')
                            ->icon('heroicon-o-star')
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('qualification.name'),
                                ImageEntry::make('qualification.image.path')
                                    ->visible(fn (?QualificationRecord $record): bool => isset($record->qualification->image))
                                    ->height(32)
                                    ->hiddenLabel(),
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
                            ->visible(fn (?QualificationRecord $record): bool => $record->document !== null)
                            ->label(fn (?QualificationRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Livewire::make(ViewDocument::class, fn (?QualificationRecord $record): array => [
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
            ->emptyStateDescription('Create a new qualification record to get started.')
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('qualification.name')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('qualification.image.path')
                    ->placeholder('No Image')
                    ->label(''),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (QualificationRecord $record) => $record->document)
                            ->user(fn (QualificationRecord $record) => $record->user)
                            ->attached(fn (QualificationRecord $record): QualificationRecord => $record),
                    ),
                TextColumn::make('text')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon('heroicon-o-document')
                    ->wrap(false)
                    ->formatStateUsing(fn ($state) => Str::limit($state, 20))
                    ->html()
                    ->sortable()
                    ->searchable()
                    ->action(
                        ViewHtmlAction::make()
                            ->modalHeading('Text')
                            ->html(fn (QualificationRecord $record) => $record->text),
                    ),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['document.name', 'qualification.name', 'user.name'])
            ->filters([
                SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('qualification')
                    ->relationship('qualification', 'name')
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
                    ->exporter(QualificationRecordExporter::class)
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
        ];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListQualificationRecords::route('/'),
            'create' => CreateQualificationRecord::route('/create'),
            'view' => ViewQualificationRecord::route('/{record}'),
            'edit' => EditQualificationRecord::route('/{record}/edit'),
        ];
    }

    /**
     * @param  QualificationRecord  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $user = optional($record->user)->name;

        return sprintf('%d: %s', $record->id, $user);
    }

    /**
     * @param  QualificationRecord  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [
            'Qualification' => optional($record->qualification)->name,
        ];

        if (filled($record->text)) {
            $details['Text'] = Str::of($record->text)->stripTags()->limit()->squish()->toString();
        }

        return $details;
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['text', 'user.name', 'qualification.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'qualification']);
    }
}
