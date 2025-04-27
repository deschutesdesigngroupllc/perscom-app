<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AssignmentRecordResource\Pages;
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
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AssignmentRecordResource extends BaseResource
{
    protected static ?string $model = AssignmentRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        /** @var DashboardSettings $settings */
        $settings = app(DashboardSettings::class);
        $rosterMode = $settings->roster_mode;

        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Details')
                            ->columns()
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label(fn ($operation): string => $operation === 'create' ? 'User(s)' : 'User')
                                    ->multiple(fn ($operation): bool => $operation === 'create')
                                    ->required()
                                    ->helperText('The user this record is assigned to.')
                                    ->preload()
                                    ->options(fn () => User::orderBy('name')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => UserResource::form($form)),
                                Forms\Components\Select::make('type')
                                    ->helperText('The type of assignment record. A primary assignment record will update the user\'s assigned unit, position, and specialty. A secondary assignment will simply add a new record to the user\'s list of secondary assignments.')
                                    ->required()
                                    ->live()
                                    ->options(AssignmentRecordType::class)
                                    ->default(AssignmentRecordType::PRIMARY),
                                Forms\Components\RichEditor::make('text')
                                    ->helperText('Optional information about the record.')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->columnSpanFull()
                                    ->default(now())
                                    ->required(),
                                Forms\Components\Select::make('document_id')
                                    ->helperText('The document for this record.')
                                    ->preload()
                                    ->relationship(name: 'document', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => DocumentResource::form($form)),
                                Forms\Components\Select::make('author_id')
                                    ->required()
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->helperText('The author of the record.')
                                    ->preload()
                                    ->relationship(name: 'author', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => UserResource::form($form)),
                            ]),
                        Forms\Components\Tabs\Tab::make('Assignment Record')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Forms\Components\Placeholder::make('warning')
                                    ->hiddenLabel()
                                    ->content(new HtmlString("<div class='font-bold'>NOTE: Updating an assignment record does not update a user's position, specialty, or unit. To make these changes, please create a new assignment record. Alternatively, you may manually update a user's position, specialty, or unit from their personnel file.</div>"))
                                    ->visibleOn('edit'),
                                Forms\Components\Select::make('position_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned the position when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => PositionResource::form($form)),
                                Forms\Components\Select::make('specialty_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned the specialty when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => SpecialtyResource::form($form)),
                                Forms\Components\Select::make('unit_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::AUTOMATIC)
                                    ->helperText('If selected, the user(s) will be assigned to the unit when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => UnitResource::form($form)),
                                Forms\Components\Select::make('unit_slot_id')
                                    ->visible(fn (): bool => $rosterMode === RosterMode::MANUAL)
                                    ->required(fn (): bool => $rosterMode === RosterMode::MANUAL)
                                    ->helperText('The slot the user will be assigned to. If the slot has an assigned position or specialty, the user will also be assigned the designated specialty and position in addition to the unit the slot is apart of.')
                                    ->label('Slot')
                                    ->preload()
                                    ->searchable()
                                    ->options(fn () => Unit::ordered()->with('slots')->get()->mapWithKeys(fn (Unit $unit) => [$unit->name => $unit->slots->pluck('name', 'pivot.id')->toArray()])->toArray()),
                                Forms\Components\Select::make('status_id')
                                    ->helperText('If selected, the user(s) will be assigned the status when the record is created.')
                                    ->preload()
                                    ->relationship(name: 'status', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => StatusResource::form($form)),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notifications')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Assignment Record')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name'),
                                Infolists\Components\TextEntry::make('type')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('position.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->position)),
                                Infolists\Components\TextEntry::make('specialty.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->specialty)),
                                Infolists\Components\TextEntry::make('unit.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->unit)),
                                Infolists\Components\TextEntry::make('status.name')
                                    ->hidden(fn (?AssignmentRecord $record): bool => is_null($record->status)),
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
                            ]),
                        Infolists\Components\Tabs\Tab::make('Document')
                            ->visible(fn (?AssignmentRecord $record): bool => $record->document !== null)
                            ->label(fn (?AssignmentRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Infolists\Components\Livewire::make(ViewDocument::class, fn (?AssignmentRecord $record): array => [
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
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('position.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('specialty.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('select')
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
            ])
            ->groups(['document.name', 'position.name', 'specialty.name', 'status.name', 'type', 'unit.name', 'user.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('position')
                    ->relationship('position', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('specialty')
                    ->relationship('specialty', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->options(AssignmentRecordType::class),
                Tables\Filters\SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(AssignmentRecordExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAssignmentRecords::route('/'),
            'create' => Pages\CreateAssignmentRecord::route('/create'),
            'view' => Pages\ViewAssignmentRecord::route('/{record}'),
            'edit' => Pages\EditAssignmentRecord::route('/{record}/edit'),
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
