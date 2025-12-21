<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Actions\ViewHtmlAction;
use App\Filament\App\Resources\AssignmentRecordResource\Pages\CreateAssignmentRecord;
use App\Filament\App\Resources\AssignmentRecordResource\Pages\EditAssignmentRecord;
use App\Filament\App\Resources\AssignmentRecordResource\Pages\ListAssignmentRecords;
use App\Filament\App\Resources\AssignmentRecordResource\Pages\ViewAssignmentRecord;
use App\Filament\App\Resources\AssignmentRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\AssignmentRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\DocumentResource\Actions\ViewDocumentAction;
use App\Filament\Exports\AssignmentRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\RosterMode;
use App\Models\Field;
use App\Models\Unit;
use App\Models\User;
use App\Settings\DashboardSettings;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class AssignmentRecordResource extends BaseResource
{
    use BuildsCustomFieldComponents;

    protected static ?string $model = AssignmentRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        /** @var DashboardSettings $settings */
        $settings = app(DashboardSettings::class);
        $rosterMode = $settings->roster_mode;

        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Assignment Record')
                            ->columns()
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                TextEntry::make('warning')
                                    ->columnSpanFull()
                                    ->hiddenLabel()
                                    ->html()
                                    ->getStateUsing(fn (): HtmlString => new HtmlString("<span class='fi-sc-text'>Updating an assignment record does not update a user's position, specialty, unit, or status. To make these automated changes, please create a new assignment record. Alternatively, you may manually update a user's position, specialty, or unit from their personnel file.</span>"))
                                    ->visibleOn('edit'),
                                Select::make('user_id')
                                    ->label(fn ($operation): string => $operation === 'create' ? 'User(s)' : 'User')
                                    ->multiple(fn ($operation): bool => $operation === 'create')
                                    ->required()
                                    ->helperText('The user this record is assigned to.')
                                    ->preload()
                                    ->options(fn () => User::orderBy('name')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UserResource::form($form)),
                                Select::make('type')
                                    ->helperText("The type of assignment record. A primary assignment record will update the user's assigned unit, position, and specialty. A secondary assignment will simply add a new record to the user's list of secondary assignments.")
                                    ->required()
                                    ->live()
                                    ->options(AssignmentRecordType::class)
                                    ->default(AssignmentRecordType::PRIMARY),
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
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Select::make('position_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned the position when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => PositionResource::form($form)),
                                Select::make('specialty_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned the specialty when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => SpecialtyResource::form($form)),
                                Select::make('unit_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned to the unit when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UnitResource::form($form)),
                                Select::make('unit_slot_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::MANUAL)
                                    ->required(fn (): bool => $rosterMode === RosterMode::MANUAL)
                                    ->helperText('The slot the user will be assigned to. If the slot has an assigned position or specialty, the user will also be assigned the designated specialty and position in addition to the unit the slot is apart of.')
                                    ->label('Slot')
                                    ->preload()
                                    ->searchable()
                                    ->options(fn () => Unit::ordered()->with('slots')->get()->mapWithKeys(fn (Unit $unit): array => [$unit->name => $unit->slots->pluck('name', 'pivot.id')->toArray()])->toArray()),
                                Select::make('status_id')
                                    ->helperText('If selected, the user(s) will be assigned the status when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'status', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => StatusResource::form($form)),
                            ]),
                        Tab::make('Fields')
                            ->icon('heroicon-o-pencil')
                            ->schema(function (): array {
                                $settings = app(FieldSettings::class);

                                $fields = collect($settings->assignment_records);

                                return AssignmentRecordResource::buildCustomFieldInputs(Field::findMany($fields));
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
                                        defaults: data_get($settings->toArray(), 'assignment_records'),
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
                        Tab::make('Assignment Record')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('type')
                                    ->badge(),
                                TextEntry::make('position.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->position)),
                                TextEntry::make('specialty.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->specialty)),
                                TextEntry::make('unit.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->unit)),
                                TextEntry::make('status.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->status)),
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

                                $fields = collect($settings->assignment_records);

                                return AssignmentRecordResource::buildCustomFieldEntries($fields);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedRectangleStack)
            ->emptyStateDescription('Create a new assignment record to get started.')
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('position.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('specialty.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('unit.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('document.name')
                    ->placeholder(new HtmlString('&ndash;'))
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        ViewDocumentAction::make()
                            ->document(fn (AssignmentRecord $record) => $record->document)
                            ->user(fn (AssignmentRecord $record) => $record->user)
                            ->attached(fn (AssignmentRecord $record): AssignmentRecord => $record),
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
                            ->html(fn (AssignmentRecord $record) => $record->text),
                    ),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['document.name', 'position.name', 'specialty.name', 'status.name', 'type', 'unit.name', 'user.name'])
            ->filters([
                SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('position')
                    ->relationship('position', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('specialty')
                    ->relationship('specialty', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('type')
                    ->options(AssignmentRecordType::class),
                SelectFilter::make('unit')
                    ->relationship('unit', 'name')
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
                    ->exporter(AssignmentRecordExporter::class)
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
            'index' => ListAssignmentRecords::route('/'),
            'create' => CreateAssignmentRecord::route('/create'),
            'view' => ViewAssignmentRecord::route('/{record}'),
            'edit' => EditAssignmentRecord::route('/{record}/edit'),
        ];
    }

    /**
     * @param  AssignmentRecord  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $user = optional($record->user)->name;

        return sprintf('%d: %s', $record->id, $user);
    }

    /**
     * @param  AssignmentRecord  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [
            'Type' => optional($record->type)->getLabel(),
            'Position' => optional($record->position)->name,
            'Specialty' => optional($record->specialty)->name,
            'Unit' => optional($record->unit)->name,
            'Status' => optional($record->status)->name,
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
        return ['text', 'position.name', 'specialty.name', 'unit.name', 'status.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'position', 'specialty', 'unit', 'status']);
    }
}
