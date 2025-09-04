<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AssignmentRecordResource\Pages\CreateAssignmentRecord;
use App\Filament\App\Resources\AssignmentRecordResource\Pages\EditAssignmentRecord;
use App\Filament\App\Resources\AssignmentRecordResource\Pages\ListAssignmentRecords;
use App\Filament\App\Resources\AssignmentRecordResource\Pages\ViewAssignmentRecord;
use App\Filament\App\Resources\AssignmentRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\AssignmentRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\AssignmentRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Livewire\App\ViewDocument;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\RosterMode;
use App\Models\Unit;
use App\Models\User;
use App\Settings\DashboardSettings;
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
use Filament\Forms\Components\Placeholder;
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

class AssignmentRecordResource extends BaseResource
{
    protected static ?string $model = AssignmentRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        /** @var DashboardSettings $settings */
        $settings = app(DashboardSettings::class);
        $rosterMode = $settings->roster_mode;

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
                                Select::make('type')
                                    ->helperText('The type of assignment record. A primary assignment record will update the user\'s assigned unit, position, and specialty. A secondary assignment will simply add a new record to the user\'s list of secondary assignments.')
                                    ->required()
                                    ->live()
                                    ->options(AssignmentRecordType::class)
                                    ->default(AssignmentRecordType::PRIMARY),
                                RichEditor::make('text')
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
                        Tab::make('Assignment Record')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Placeholder::make('warning')
                                    ->hiddenLabel()
                                    ->content(new HtmlString("<div class='font-bold'>NOTE: Updating an assignment record does not update a user's position, specialty, or unit. To make these changes, please create a new assignment record. Alternatively, you may manually update a user's position, specialty, or unit from their personnel file.</div>"))
                                    ->visibleOn('edit'),
                                Select::make('position_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned the position when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => PositionResource::form($form)),
                                Select::make('specialty_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned the specialty when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => SpecialtyResource::form($form)),
                                Select::make('unit_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned to the unit when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => UnitResource::form($form)),
                                Select::make('unit_slot_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::MANUAL)
                                    ->required(fn (): bool => $rosterMode === RosterMode::MANUAL)
                                    ->helperText('The slot the user will be assigned to. If the slot has an assigned position or specialty, the user will also be assigned the designated specialty and position in addition to the unit the slot is apart of.')
                                    ->label('Slot')
                                    ->preload()
                                    ->searchable()
                                    ->options(fn () => Unit::ordered()->with('slots')->get()->mapWithKeys(fn (Unit $unit) => [$unit->name => $unit->slots->pluck('name', 'pivot.id')->toArray()])->toArray()),
                                Select::make('status_id')
                                    ->helperText('If selected, the user(s) will be assigned the status when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'status', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Schema => StatusResource::form($form)),
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
                                TextEntry::make('author.name'),
                                TextEntry::make('created_at'),
                                TextEntry::make('updated_at'),
                            ]),
                        Tab::make('Document')
                            ->visible(fn (?AssignmentRecord $record): bool => $record->document !== null)
                            ->label(fn (?AssignmentRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Livewire::make(ViewDocument::class, fn (?AssignmentRecord $record): array => [
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
                    ->sortable()
                    ->searchable(),
                TextColumn::make('specialty.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('unit.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Action::make('select')
                            ->visible(fn (?AssignmentRecord $record): bool => $record->document !== null)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?AssignmentRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?AssignmentRecord $record) => view('app.view-document', [
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

        return "$record->id: $user";
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['text', 'position.name', 'specialty.name', 'unit.name', 'status.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'position', 'specialty', 'unit', 'status']);
    }
}
