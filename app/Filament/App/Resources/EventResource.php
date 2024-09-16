<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\EventResource\Pages;
use App\Filament\App\Resources\EventResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\EventResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Filament\Exports\EventExporter;
use App\Models\Event;
use App\Services\EventService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class EventResource extends BaseResource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Calendar';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Event')
                            ->icon('heroicon-o-calendar-days')
                            ->columns()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the event.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('calendar_id')
                                    ->preload()
                                    ->helperText('The calendar the event belongs to.')
                                    ->required()
                                    ->relationship(name: 'calendar', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form) => CalendarResource::form($form)),
                                Forms\Components\Select::make('author_id')
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->preload()
                                    ->helperText('The author of the event.')
                                    ->required()
                                    ->relationship(name: 'author', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form) => UserResource::form($form)),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('The description of the event.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('location')
                                    ->helperText('The location of the event.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('url')
                                    ->helperText('An optional URL for the event.')
                                    ->url()
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->helperText('The informational content of the event.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Image')
                            ->visibleOn('edit')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->hiddenLabel()
                                    ->relationship('image', fn ($state) => filled(data_get($state, 'path')))
                                    ->schema([
                                        Forms\Components\FileUpload::make('path')
                                            ->hiddenLabel()
                                            ->image()
                                            ->imageEditor()
                                            ->previewable()
                                            ->openable()
                                            ->downloadable()
                                            ->visibility('public')
                                            ->storeFileNamesIn('filename')
                                            ->disk('s3')
                                            ->helperText('Add an optional image for the event.'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Scheduling')
                            ->icon('heroicon-o-clock')
                            ->columns()
                            ->schema([
                                Forms\Components\DateTimePicker::make('start')
                                    ->default(now()->addHour()->startOfHour()->toDateTimeLocalString())
                                    ->live()
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => ! $get('all_day')),
                                Forms\Components\DateTimePicker::make('end')
                                    ->live()
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => ! $get('all_day') && ! $get('repeats')),
                                Forms\Components\DatePicker::make('start')
                                    ->default(today()->toDateString())
                                    ->live()
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => $get('all_day')),
                                Forms\Components\DatePicker::make('end')
                                    ->live()
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => $get('all_day') && ! $get('repeats') || ($get('all_day') && $get('repeats'))),
                                Forms\Components\TimePicker::make('end')
                                    ->live()
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => $get('repeats') && ! $get('all_day')),
                                Forms\Components\Toggle::make('all_day')
                                    ->helperText('Check if the event does not have a start and end time.')
                                    ->columnSpanFull()
                                    ->live()
                                    ->required(),
                                Forms\Components\Toggle::make('repeats')
                                    ->helperText('Check if the event repeats.')
                                    ->columnSpanFull()
                                    ->default(false)
                                    ->live()
                                    ->required(),
                                Forms\Components\Grid::make()
                                    ->columnSpanFull()
                                    ->columns(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('interval')
                                            ->helperText('The interval at which the event will repeat.')
                                            ->numeric()
                                            ->requiredIf('repeats', true)
                                            ->default(1)
                                            ->hidden(fn (Forms\Get $get) => ! $get('repeats')),
                                        Forms\Components\Select::make('frequency')
                                            ->live()
                                            ->helperText('The frequency at which the event will repeat.')
                                            ->requiredIf('repeats', true)
                                            ->options([
                                                'DAILY' => 'Day(s)',
                                                'WEEKLY' => 'Week(s)',
                                                'MONTHLY' => 'Month(s)',
                                                'YEARLY' => 'Year(s)',
                                            ])
                                            ->default('WEEKLY')
                                            ->hidden(fn (Forms\Get $get) => ! $get('repeats')),
                                        Forms\Components\Select::make('by_day')
                                            ->helperText('The day(s) of the week the event will repeat.')
                                            ->requiredIf('frequency', 'WEEKLY')
                                            ->multiple()
                                            ->label('On')
                                            ->default('never')
                                            ->options([
                                                'SU' => 'Sunday',
                                                'MO' => 'Monday',
                                                'TU' => 'Tuesday',
                                                'WE' => 'Wednesday',
                                                'TH' => 'Thursday',
                                                'FR' => 'Friday',
                                                'SA' => 'Saturday',
                                            ])
                                            ->hidden(fn (Forms\Get $get) => ! $get('repeats') || $get('frequency') === 'DAILY' || $get('frequency') === 'MONTHLY' || $get('frequency') === 'YEARLY'),
                                        Forms\Components\Select::make('by_month_day')
                                            ->helperText('The day of the month the event will repeat.')
                                            ->requiredIf('frequency', 'MONTHLY')
                                            ->multiple()
                                            ->label('On')
                                            ->default('never')
                                            ->options(function (Forms\Get $get) {
                                                $month = Carbon::parse($get('start'))->startOfMonth();
                                                $period = collect($month->toPeriod($month->copy()->endOfMonth(), 1, 'day')->settings([
                                                    'monthOverflow' => false,
                                                ]));

                                                return $period->mapWithKeys(function ($value) {
                                                    return [$value->format('j') => $value->format('jS')];
                                                });
                                            })
                                            ->hidden(fn (Forms\Get $get) => ! $get('repeats') || $get('frequency') === 'DAILY' || $get('frequency') === 'WEEKLY' || $get('frequency') === 'YEARLY'),
                                        Forms\Components\Select::make('by_month')
                                            ->helperText('The month of the year the event will repeat.')
                                            ->multiple()
                                            ->label('On')
                                            ->default('never')
                                            ->options([
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December',
                                            ])
                                            ->hidden(fn (Forms\Get $get) => ! $get('repeats') || $get('frequency') === 'DAILY' || $get('frequency') === 'WEEKLY' || $get('frequency') === 'MONTHLY'),
                                    ]),
                                Forms\Components\Select::make('end_type')
                                    ->columnSpan(fn ($state) => in_array($state, ['on', 'after']) ? 1 : 2)
                                    ->helperText('When the event will end.')
                                    ->live()
                                    ->label('Ends')
                                    ->default('never')
                                    ->options([
                                        'never' => 'Never',
                                        'on' => 'On',
                                        'after' => 'After',
                                    ])
                                    ->requiredIf('repeats', true)
                                    ->hidden(fn (Forms\Get $get) => ! $get('repeats')),
                                Forms\Components\TextInput::make('count')
                                    ->default(1)
                                    ->label('Occurrences')
                                    ->helperText('The number of occurrences before the recurring event ends.')
                                    ->hidden(fn (Forms\Get $get) => $get('end_type') !== 'after')
                                    ->required(fn (Forms\Get $get) => $get('end_type') === 'after')
                                    ->numeric(),
                                Forms\Components\DatePicker::make('until')
                                    ->default(today()->addMonth())
                                    ->label('End Date')
                                    ->helperText('The date the recurring event will end.')
                                    ->hidden(fn (Forms\Get $get) => $get('end_type') !== 'on')
                                    ->required(fn (Forms\Get $get) => $get('end_type') === 'on'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Registration')
                            ->icon('heroicon-o-user-plus')
                            ->schema([
                                Forms\Components\Toggle::make('registration_enabled')
                                    ->label('Enabled')
                                    ->helperText('Whether registration is enabled for the event.')
                                    ->required()
                                    ->default(true),
                                Forms\Components\DateTimePicker::make('registration_deadline')
                                    ->label('Deadline')
                                    ->nullable()
                                    ->helperText('The deadline for registration.'),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Event')
                            ->badge(fn (?Event $record) => $record->all_day ? 'All Day' : null)
                            ->badgeColor('success')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                TextEntry::make('name')
                                    ->badge()
                                    ->color(fn (?Event $record) => Color::hex($record->calendar?->color)),
                                TextEntry::make('description')
                                    ->hidden(fn ($state) => is_null($state))
                                    ->hiddenLabel()
                                    ->html(),
                                TextEntry::make('start')
                                    ->label('Starts')
                                    ->icon(fn (?Event $record) => $record->repeats ? 'heroicon-o-arrow-path' : null)
                                    ->suffix(fn (?Event $record) => $record->length ? " ({$record->length->forHumans(['parts' => 1])})" : null)
                                    ->formatStateUsing(function (?Event $record, $component) {
                                        return match ($record->all_day) {
                                            true => Carbon::parse($record->start)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateDisplayFormat),
                                            false => Carbon::parse($record->start)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateTimeDisplayFormat)
                                        };
                                    }),
                                TextEntry::make('end')
                                    ->label('Ends')
                                    ->hidden(fn (?Event $record) => is_null($record->last_occurrence))
                                    ->formatStateUsing(function (?Event $record, $component) {
                                        return match ($record->all_day) {
                                            true => Carbon::parse($record->last_occurrence)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateDisplayFormat),
                                            false => Carbon::parse($record->last_occurrence)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateTimeDisplayFormat)
                                        };
                                    }),
                                TextEntry::make('rule_readable')
                                    ->visible(fn (?Event $record) => $record->repeats)
                                    ->label('Pattern')
                                    ->getStateUsing(function (?Event $record) {
                                        $rule = EventService::generateRecurringRule($record);

                                        return Str::title($rule->humanReadable());
                                    }),
                            ]),
                        Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('content')
                                    ->label('Details')
                                    ->html(),
                                TextEntry::make('location'),
                                TextEntry::make('url')
                                    ->url(fn (?Event $record) => $record->url),
                            ]),
                        Tabs\Tab::make('Registration')
                            ->badge(fn (?Event $record) => $record->registration_enabled ? $record->registrations()->count() : null)
                            ->badgeColor('info')
                            ->icon('heroicon-o-user-plus')
                            ->visible(fn (?Event $record) => $record->registration_enabled)
                            ->schema([
                                TextEntry::make('registration_deadline')
                                    ->label('Deadline')
                                    ->hidden(fn (?Event $record) => is_null($record->registration_deadline))
                                    ->dateTime(),
                                Livewire::make(RegistrationsRelationManager::class, fn (?Event $record) => [
                                    'ownerRecord' => $record,
                                    'pageClass' => Pages\ViewEvent::class,
                                ])->key('event-registrations'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->icon(fn (?Event $record) => $record->repeats ? 'heroicon-o-arrow-path' : null),
                Tables\Columns\TextColumn::make('calendar.name')
                    ->badge()
                    ->color(fn (?Event $record) => Color::hex($record->calendar?->color)),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Organizer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start')
                    ->label('Starts')
                    ->formatStateUsing(function (?Event $record, $column) {
                        return match ($record->all_day) {
                            true => Carbon::parse($record->start)->setTimezone($column->getTimezone())->translatedFormat(Table::$defaultDateDisplayFormat),
                            false => Carbon::parse($record->start)->setTimezone($column->getTimezone())->translatedFormat(Table::$defaultDateTimeDisplayFormat)
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_occurrence')
                    ->label('Ends')
                    ->formatStateUsing(function (?Event $record, $column) {
                        return match ($record->all_day) {
                            true => Carbon::parse($record->last_occurrence)->setTimezone($column->getTimezone())->translatedFormat(Table::$defaultDateDisplayFormat),
                            false => Carbon::parse($record->last_occurrence)->setTimezone($column->getTimezone())->translatedFormat(Table::$defaultDateTimeDisplayFormat)
                        };
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('all_day')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordClasses(fn (?Event $record) => match ($record->has_passed) {
                true => '!border-s-2 !border-s-red-600',
                default => null,
            })
            ->groups(['all_day', 'repeats'])
            ->filters([
                Tables\Filters\TernaryFilter::make('all_day'),
                Tables\Filters\SelectFilter::make('calendar')
                    ->preload()
                    ->multiple()
                    ->relationship('calendar', 'name'),
                Tables\Filters\SelectFilter::make('author')
                    ->label('Organizer')
                    ->preload()
                    ->multiple()
                    ->relationship('author', 'name'),
                Tables\Filters\TernaryFilter::make('repeats'),
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
                        ->exporter(EventExporter::class),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
            'view' => Pages\ViewEvent::route('/{record}'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model|Event $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model|Event $record): array
    {
        return [
            'Description' => Str::limit($record->description),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'content'];
    }
}
