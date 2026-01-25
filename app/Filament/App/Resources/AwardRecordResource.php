<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\AwardRecordResource\Pages\CreateAwardRecord;
use App\Filament\App\Resources\AwardRecordResource\Pages\EditAwardRecord;
use App\Filament\App\Resources\AwardRecordResource\Pages\ListAwardRecords;
use App\Filament\App\Resources\AwardRecordResource\Pages\ViewAwardRecord;
use App\Filament\App\Resources\AwardRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\AwardRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\Exports\AwardRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Models\AwardRecord;
use App\Models\Field;
use App\Models\User;
use App\Settings\FieldSettings;
use App\Settings\NotificationSettings;
use App\Traits\Filament\BuildsCustomFieldComponents;
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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
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

class AwardRecordResource extends BaseResource
{
    use BuildsCustomFieldComponents;

    protected static ?string $model = AwardRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Award Record')
                            ->columns()
                            ->icon('heroicon-o-trophy')
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
                                Select::make('award_id')
                                    ->required()
                                    ->helperText('The award for this record.')
                                    ->preload()
                                    ->relationship(name: 'award', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => AwardResource::form($form)),
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
                        Tab::make('Fields')
                            ->icon('heroicon-o-pencil')
                            ->schema(function (): array {
                                $settings = app(FieldSettings::class);

                                $fields = collect($settings->award_records);

                                return AwardRecordResource::buildCustomFieldInputs(Field::findMany($fields));
                            }),
                        Tab::make('Notifications')
                            ->visibleOn('create')
                            ->icon('heroicon-o-bell')
                            ->schema(function (): array {
                                /** @var NotificationSettings $settings */
                                $settings = app(NotificationSettings::class);

                                return [
                                    ModelNotification::make(
                                        alert: new HtmlString("<div class='font-bold max-w-2xl'>The recipients will already receive a notification about the new record. Default notification configuration can be set in your system settings.</div>"),
                                        defaults: data_get($settings->toArray(), 'award_records'),
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
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Award Record')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User'),
                                TextEntry::make('award.name')
                                    ->label('Award'),
                                ImageEntry::make('award.image.path')
                                    ->visible(fn (?AwardRecord $record): bool => isset($record->award->image))
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
                                TextEntry::make('author.name')
                                    ->label('Author'),
                                TextEntry::make('created_at'),
                                TextEntry::make('updated_at'),
                            ]),
                        Tab::make('Fields')
                            ->icon('heroicon-o-pencil')
                            ->schema(function (): array {
                                $settings = app(FieldSettings::class);

                                $fields = collect($settings->award_records);

                                return AwardRecordResource::buildCustomFieldEntries(Field::findMany($fields));
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedTrophy)
            ->emptyStateDescription('Create a new award record to get started.')
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('award.name')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('award.image.path')
                    ->placeholder('No Image')
                    ->label(''),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (AwardRecord $record) => $record->document)
                            ->user(fn (AwardRecord $record) => $record->user)
                            ->attached(fn (AwardRecord $record): AwardRecord => $record),
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
                            ->html(fn (AwardRecord $record) => $record->text),
                    ),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['award.name', 'document.name', 'user.name'])
            ->filters([
                SelectFilter::make('award')
                    ->relationship('award', 'name')
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
                    ->exporter(AwardRecordExporter::class)
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
            'index' => ListAwardRecords::route('/'),
            'create' => CreateAwardRecord::route('/create'),
            'view' => ViewAwardRecord::route('/{record}'),
            'edit' => EditAwardRecord::route('/{record}/edit'),
        ];
    }

    /**
     * @param  AwardRecord  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $user = optional($record->user)->name;

        return sprintf('%d: %s', $record->id, $user);
    }

    /**
     * @param  AwardRecord  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [
            'Award' => optional($record->award)->name,
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
        return ['text', 'user.name', 'award.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'award']);
    }
}
