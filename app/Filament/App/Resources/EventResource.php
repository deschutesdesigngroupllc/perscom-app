<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\Categories\Schemas\CategoryForm;
use App\Filament\App\Resources\EventResource\Pages\CreateEvent;
use App\Filament\App\Resources\EventResource\Pages\EditEvent;
use App\Filament\App\Resources\EventResource\Pages\ListEvents;
use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Filament\App\Resources\EventResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Filament\Exports\EventExporter;
use App\Forms\Components\Schedule;
use App\Models\Calendar;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\Schedule as ScheduleModel;
use App\Models\User;
use App\Services\ScheduleService;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use BackedEnum;
use Carbon\CarbonInterface;
use Carbon\Month;
use Carbon\WeekDay;
use DateTimeInterface;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use UnitEnum;

class EventResource extends BaseResource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Calendar';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        $timezone = fn (): string => UserSettingsService::get('timezone', function (): string {
            /** @var OrganizationSettings $settings */
            $settings = resolve(OrganizationSettings::class);

            return $settings->timezone ?? config('app.timezone');
        });

        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Basics')
                            ->columns()
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the event.')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->columnSpanFull(),
                                Select::make('calendar_id')
                                    ->label('Calendar')
                                    ->preload()
                                    ->helperText('The calendar the event belongs to.')
                                    ->required()
                                    ->live()
                                    ->relationship(name: 'calendar', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => CalendarResource::form($form)),
                                Select::make('categories')
                                    ->label('Category')
                                    ->createOptionForm(fn (Schema $schema): Schema => CategoryForm::configure($schema))
                                    ->editOptionForm(fn (Schema $schema): Schema => CategoryForm::configure($schema))
                                    ->helperText('The category the event belongs to.')
                                    ->nullable()
                                    ->preload()
                                    ->searchable()
                                    ->multiple()
                                    ->maxItems(1)
                                    ->relationship('categories', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->where('resource', static::$model)),
                                Select::make('author_id')
                                    ->label('Host')
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->preload()
                                    ->helperText('The member hosting the event.')
                                    ->required()
                                    ->relationship(name: 'author', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UserResource::form($form))
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 6rem;'])
                                    ->helperText('A short summary of the event, shown in lists and search results.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                RichEditor::make('content')
                                    ->label('Briefing')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('The full briefing shown on the event page.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Section::make()
                                    ->contained(false)
                                    ->visibleOn('edit')
                                    ->columnSpanFull()
                                    ->relationship('image', fn ($state): bool => filled(data_get($state, 'path')))
                                    ->schema([
                                        FileUpload::make('path')
                                            ->label('Image')
                                            ->image()
                                            ->imageEditor()
                                            ->previewable()
                                            ->openable()
                                            ->downloadable()
                                            ->visibility('public')
                                            ->storeFileNamesIn('filename')
                                            ->helperText('Add an optional image for the event.'),
                                    ]),
                            ]),
                        Section::make('When & Where')
                            ->columns()
                            ->schema(fn (Get $get): array => [
                                DateTimePicker::make('starts')
                                    ->helperText('The date and time the event starts.')
                                    ->timezone($timezone())
                                    ->default(now()->addHour()->startOfHour())
                                    ->live(onBlur: true)
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, Get $get, DateTimeInterface|WeekDay|Month|string|int|float|null $state, $component): void {
                                        $date = Date::parse($state)
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $set('schedule.start', $date);

                                        $start = Date::parse($state);
                                        $end = Date::parse($get('ends') ?? $state);

                                        if ($get('all_day')) {
                                            $set('ends', $state);
                                        } elseif ($start->isAfter($end)) {
                                            $set('ends', $start->copy()->addHour()->toDateTimeString());
                                        }
                                    })
                                    ->time(fn (Get $get): bool => ! $get('all_day')),
                                DateTimePicker::make('ends')
                                    ->helperText('The date and time the event ends.')
                                    ->timezone($timezone())
                                    ->default(now()->addHours(2)->startOfHour())
                                    ->afterOrEqual('starts')
                                    ->live(onBlur: true)
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, Get $get, DateTimeInterface|WeekDay|Month|string|int|float|null $state, $component): void {
                                        $start = Date::parse($get('starts'))
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $end = Date::parse($state)
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $set('schedule.duration', $start->diffInHours($end));
                                    })
                                    ->time(fn (Get $get): bool => ! $get('all_day')),
                                Toggle::make('all_day')
                                    ->helperText('Check if the event does not have a start and end time.')
                                    ->live()
                                    ->required(),
                                Toggle::make('repeats')
                                    ->helperText('Check if the event repeats.')
                                    ->default(false)
                                    ->live(),
                                Schedule::make(
                                    startHidden: true,
                                    allDay: $get('all_day') ?? false
                                )->visible($get('repeats') ?? false),
                                Textarea::make('location')
                                    ->helperText('The location of the event.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->live(onBlur: true)
                                    ->columnSpanFull(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->helperText('An optional URL for the event.')
                                    ->url()
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Registration & Reminders')
                            ->columns()
                            ->schema([
                                Toggle::make('registration_enabled')
                                    ->live()
                                    ->label('Registration enabled')
                                    ->helperText('Whether members can RSVP to the event.')
                                    ->default(false),
                                DateTimePicker::make('registration_deadline')
                                    ->visible(fn (Get $get): mixed => $get('registration_enabled'))
                                    ->timezone($timezone())
                                    ->label('Deadline')
                                    ->nullable()
                                    ->helperText('The deadline for registration. Leave blank for no deadline.'),
                                Toggle::make('creates_training_record')
                                    ->label('Create training records')
                                    ->helperText('Automatically create a training record for each member marked as attended.')
                                    ->default(false)
                                    ->columnSpanFull(),
                                Toggle::make('notifications_enabled')
                                    ->live()
                                    ->label('Reminders enabled')
                                    ->validationAttribute('enabled notifications')
                                    ->default(false)
                                    ->helperText('Send reminder notifications to all registered users.')
                                    ->columnSpanFull(),
                                Select::make('notifications_interval')
                                    ->visible(fn (Get $get): mixed => $get('notifications_enabled'))
                                    ->label('Alert')
                                    ->helperText('When should the notifications be sent.')
                                    ->requiredIf('notifications_enabled', true)
                                    ->multiple()
                                    ->options(NotificationInterval::class),
                                CheckboxList::make('notifications_channels')
                                    ->visible(fn (Get $get): mixed => $get('notifications_enabled'))
                                    ->label('Channels')
                                    ->helperText('How the notifications should be delivered.')
                                    ->requiredIf('notifications_enabled', true)
                                    ->bulkToggleable()
                                    ->descriptions(fn () => collect(NotificationChannel::cases())->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getDescription()])->all())
                                    ->options(fn () => collect(NotificationChannel::cases())->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled())->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getLabel()])->all()),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->extraAttributes(['class' => 'hidden lg:sticky lg:top-24 lg:block'])
                    ->schema([
                        View::make('filament.app.events.form-summary')
                            ->viewData(fn (Get $get): array => static::formSummary($get)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedCalendarDays)
            ->emptyStateDescription('There are no events to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->icon(fn (Event $record): ?string => $record->repeats ? 'heroicon-o-arrow-path' : null),
                TextColumn::make('calendar.name')
                    ->badge()
                    ->color(fn (Event $record): array => Color::generateV3Palette($record->calendar?->color)),
                TextColumn::make('categories.name')
                    ->placeholder('No Categories')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
                TextColumn::make('author.display_name')
                    ->label('Organizer')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy(User::select('name')->whereColumn('users.id', 'events.author_id'), $direction)),
                IconColumn::make('all_day')
                    ->label('All Day')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordClasses(fn (Event $record): ?string => match ($record->has_passed) {
                true => 'border-s-2! border-s-red-600!',
                default => null,
            })
            ->groups(['all_day', 'calendar.name', 'repeats'])
            ->filters([
                TernaryFilter::make('all_day'),
                SelectFilter::make('calendar')
                    ->preload()
                    ->multiple()
                    ->relationship('calendar', 'name'),
                SelectFilter::make('author')
                    ->label('Organizer')
                    ->preload()
                    ->multiple()
                    ->relationship('author', 'name'),
                TernaryFilter::make('repeats'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(EventExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup('calendar.name');
    }

    public static function getRelations(): array
    {
        return [
            AttachmentsRelationManager::class,
            RegistrationsRelationManager::class,
        ];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
            'view' => ViewEvent::route('/{record}'),
        ];
    }

    /**
     * @param  Event  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Event  $record
     * @return string[]
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (blank($record->description)) {
            return [];
        }

        return [
            Str::of($record->description)->stripTags()->limit()->squish()->toString(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'content'];
    }

    /**
     * The values shown in the live summary beside the event form.
     *
     * @return array{name: ?string, calendar: ?Calendar, starts: ?CarbonInterface, ends: ?CarbonInterface, allDay: bool, repeatRule: ?string, location: ?string, registrationEnabled: bool, remindersEnabled: bool}
     */
    protected static function formSummary(Get $get): array
    {
        $parse = fn (mixed $value): ?CarbonInterface => filled($value) && is_string($value) ? rescue(fn (): CarbonInterface => Date::parse($value), report: false) : null;
        $allDay = (bool) $get('all_day');
        $starts = $parse($get('starts'));

        return [
            'name' => $get('name'),
            'calendar' => filled($get('calendar_id')) ? Calendar::query()->find($get('calendar_id')) : null,
            'starts' => $starts,
            'ends' => $parse($get('ends')),
            'allDay' => $allDay,
            'repeatRule' => $get('repeats') && $starts instanceof CarbonInterface
                ? rescue(fn (): ?string => ScheduleService::getSchedulePattern(new ScheduleModel([
                    'start' => $starts->copy()->shiftTimezone(UserSettingsService::timezone())->setTimezone(config('app.timezone')),
                    'frequency' => $get('schedule.frequency'),
                    'interval' => $get('schedule.interval'),
                    'end_type' => $get('schedule.end_type'),
                    'count' => $get('schedule.count'),
                    'until' => $get('schedule.until'),
                    'by_day' => $get('schedule.by_day'),
                    'by_month_day' => $get('schedule.by_month_day'),
                    'by_month' => $get('schedule.by_month'),
                ]), $allDay), report: false)
                : null,
            'location' => $get('location'),
            'registrationEnabled' => (bool) $get('registration_enabled'),
            'remindersEnabled' => (bool) $get('notifications_enabled'),
        ];
    }
}
