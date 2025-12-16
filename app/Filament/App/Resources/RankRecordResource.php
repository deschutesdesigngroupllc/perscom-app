<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\App\Resources\RankRecordResource\Pages\CreateRankRecord;
use App\Filament\App\Resources\RankRecordResource\Pages\EditRankRecord;
use App\Filament\App\Resources\RankRecordResource\Pages\ListRankRecords;
use App\Filament\App\Resources\RankRecordResource\Pages\ViewRankRecord;
use App\Filament\App\Resources\RankRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\RankRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\RankRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Livewire\Filament\App\ViewDocument;
use App\Models\Enums\RankRecordType;
use App\Models\Field;
use App\Models\RankRecord;
use App\Models\User;
use App\Settings\FieldSettings;
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
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
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

class RankRecordResource extends BaseResource
{
    protected static ?string $model = RankRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chevron-double-up';

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
                                Select::make('rank_id')
                                    ->required()
                                    ->helperText('The rank for this record.')
                                    ->preload()
                                    ->relationship(name: 'rank', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => RankResource::form($form)),
                                Select::make('type')
                                    ->helperText('The type of rank record.')
                                    ->columnSpanFull()
                                    ->options(RankRecordType::class)
                                    ->required()
                                    ->default(RankRecordType::PROMOTION),
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
                        Tab::make('Custom Fields')
                            ->icon('heroicon-o-pencil')
                            ->schema(function () {
                                $settings = app(FieldSettings::class);

                                $fields = collect($settings->rank_records);

                                if ($fields->isEmpty()) {
                                    return [
                                        TextEntry::make('empty')
                                            ->color(Color::Gray)
                                            ->hiddenLabel()
                                            ->columnSpanFull()
                                            ->getStateUsing(fn (): string => 'There are no custom fields assigned to this resource.'),
                                    ];
                                }

                                return $fields
                                    ->map(fn (int $fieldId) => Field::find($fieldId))
                                    ->filter()
                                    ->map(fn (Field $field) => $field->type->getFilamentField('data.'.$field->key, $field))
                                    ->toArray();
                            }),
                        Tab::make('Notifications')
                            ->visible(fn ($operation): bool => $operation === 'create')
                            ->icon('heroicon-o-bell')
                            ->schema(function (): array {
                                /** @var NotificationSettings $settings */
                                $settings = app(NotificationSettings::class);

                                return [
                                    ModelNotification::make(
                                        alert: new HtmlString("<div class='font-bold max-w-2xl'>The recipients will already receive a notification about the new record. Default notification configuration can be set in your system settings.</div>"),
                                        defaults: data_get($settings->toArray(), 'rank_records'),
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
                        Tab::make('Rank Record')
                            ->icon('heroicon-o-chevron-double-up')
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('rank.name'),
                                ImageEntry::make('rank.image.path')
                                    ->visible(fn (?RankRecord $record): bool => isset($record->rank->image))
                                    ->height(32)
                                    ->hiddenLabel(),
                                TextEntry::make('type')
                                    ->badge(),
                                TextEntry::make('text')
                                    ->html()
                                    ->prose()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Custom Fields')
                            ->icon('heroicon-o-pencil')
                            ->schema(function () {
                                $settings = app(FieldSettings::class);

                                $fields = collect($settings->rank_records);

                                if ($fields->isEmpty()) {
                                    return [
                                        TextEntry::make('empty')
                                            ->color(Color::Gray)
                                            ->hiddenLabel()
                                            ->columnSpanFull()
                                            ->getStateUsing(fn (): string => 'There are no custom fields assigned to this resource.'),
                                    ];
                                }

                                return $fields
                                    ->map(fn (int $fieldId) => Field::find($fieldId))
                                    ->filter()
                                    ->map(fn (Field $field) => $field->type->getFilamentEntry($field->key))
                                    ->toArray();
                            }),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('author.name'),
                                TextEntry::make('created_at'),
                                TextEntry::make('updated_at'),
                            ]),
                        Tab::make('Document')
                            ->visible(fn (?RankRecord $record): bool => $record->document !== null)
                            ->label(fn (?RankRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Livewire::make(ViewDocument::class, fn (?RankRecord $record): array => [
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
            ->emptyStateIcon(Heroicon::OutlinedChevronDoubleUp)
            ->emptyStateDescription('Create a new rank record to get started.')
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('rank.name')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('rank.image.path')
                    ->placeholder('No Image')
                    ->label(''),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (RankRecord $record) => $record->document)
                            ->user(fn (RankRecord $record) => $record->user)
                            ->attached(fn (RankRecord $record): RankRecord => $record),
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
                            ->html(fn (RankRecord $record) => $record->text),
                    ),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['document.name', 'rank.name', 'type', 'user.name'])
            ->filters([
                SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('rank')
                    ->relationship('rank', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('type')
                    ->options(RankRecordType::class)
                    ->multiple()
                    ->searchable()
                    ->preload(),
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
                    ->exporter(RankRecordExporter::class)
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
            'index' => ListRankRecords::route('/'),
            'create' => CreateRankRecord::route('/create'),
            'view' => ViewRankRecord::route('/{record}'),
            'edit' => EditRankRecord::route('/{record}/edit'),
        ];
    }

    /**
     * @param  RankRecord  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $user = optional($record->user)->name;

        return sprintf('%d: %s', $record->id, $user);
    }

    /**
     * @param  RankRecord  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [
            'Rank' => optional($record->rank)->name,
            'Type' => optional($record->type)->getLabel(),
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
        return ['text', 'user.name', 'rank.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'rank']);
    }
}
