<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EventResource\Pages\CreateEvent;
use App\Filament\App\Resources\EventResource\Pages\EditEvent;
use App\Filament\App\Resources\EventResource\Pages\ListEvents;
use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Filament\App\Resources\EventResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\EventResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Filament\Exports\EventExporter;
use App\Forms\Components\Schedule;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Services\ScheduleService;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use BackedEnum;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class EventResource extends BaseResource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Calendar';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Event')
                            ->icon('heroicon-o-calendar-days')
                            ->columns()
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the event.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Select::make('calendar_id')
                                    ->preload()
                                    ->helperText('The calendar the event belongs to.')
                                    ->required()
                                    ->relationship(name: 'calendar', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => CalendarResource::form($form)),
                                Select::make('author_id')
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->preload()
                                    ->helperText('The author of the event.')
                                    ->required()
                                    ->relationship(name: 'author', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UserResource::form($form)),
                                DateTimePicker::make('starts')
                                    ->helperText('The date and time the event starts.')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->default(now()->addHour()->startOfHour())
                                    ->live(onBlur: true)
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, Get $get, DateTimeInterface|\Carbon\WeekDay|\Carbon\Month|string|int|float|null $state, $component): void {
                                        $date = Carbon::parse($state)
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $set('schedule.start', $date);

                                        $start = Carbon::parse($state);
                                        $end = Carbon::parse($get('ends') ?? $state);

                                        if ($get('all_day')) {
                                            $set('ends', $state);
                                        } elseif ($start->isAfter($end)) {
                                            $set('ends', $start->copy()->addHour()->toDateTimeString());
                                        }
                                    })
                                    ->time(fn (Get $get): bool => ! $get('all_day')),
                                DateTimePicker::make('ends')
                                    ->helperText('The date and time the event ends.')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->default(now()->addHours(2)->startOfHour())
                                    ->afterOrEqual('starts')
                                    ->live(onBlur: true)
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, Get $get, DateTimeInterface|\Carbon\WeekDay|\Carbon\Month|string|int|float|null $state, $component): void {
                                        $start = Carbon::parse($get('starts'))
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $end = Carbon::parse($state)
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $set('schedule.duration', $start->diffInHours($end));
                                    })
                                    ->time(fn (Get $get): bool => ! $get('all_day')),
                                Toggle::make('all_day')
                                    ->helperText('Check if the event does not have a start and end time.')
                                    ->columnSpanFull()
                                    ->live()
                                    ->required(),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('The description of the event.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Textarea::make('location')
                                    ->helperText('The location of the event.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->helperText('An optional URL for the event.')
                                    ->url()
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                RichEditor::make('content')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->hiddenLabel()
                                    ->helperText('The informational content of the event.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Image')
                            ->visibleOn('edit')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make()
                                    ->hiddenLabel()
                                    ->relationship('image', fn ($state) => filled(data_get($state, 'path')))
                                    ->schema([
                                        FileUpload::make('path')
                                            ->hiddenLabel()
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
                        Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Toggle::make('notifications_enabled')
                                    ->live()
                                    ->label('Enabled')
                                    ->validationAttribute('enabled notifications')
                                    ->default(false)
                                    ->helperText('Send reminder notifications to all registered users.'),
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
                                    ->requiredIf('notifications_enabled', true)
                                    ->bulkToggleable()
                                    ->descriptions(fn () => collect(NotificationChannel::cases())->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getDescription()])->toArray())
                                    ->options(fn () => collect(NotificationChannel::cases())->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled())->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getLabel()])->toArray()),
                            ]),
                        Tab::make('Schedule')
                            ->icon('heroicon-o-arrow-path')
                            ->columns()
                            ->schema(fn (Get $get): array => [
                                Toggle::make('repeats')
                                    ->helperText('Check if the event repeats.')
                                    ->columnSpanFull()
                                    ->default(false)
                                    ->live(),
                                Schedule::make(
                                    startHidden: true,
                                    allDay: $get('all_day') ?? false
                                )->visible($get('repeats') ?? false),
                            ]),
                        Tab::make('Registration')
                            ->icon('heroicon-o-user-plus')
                            ->schema([
                                Toggle::make('registration_enabled')
                                    ->live()
                                    ->label('Enabled')
                                    ->helperText('Whether registration is enabled for the event.')
                                    ->default(false),
                                DateTimePicker::make('registration_deadline')
                                    ->visible(fn (Get $get): mixed => $get('registration_enabled'))
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Deadline')
                                    ->nullable()
                                    ->helperText('The deadline for registration. Leave blank for no deadline.'),
                            ]),
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
                        Tab::make('Event')
                            ->badge(fn (?Event $record): ?string => $record->all_day ? 'All Day' : null)
                            ->badgeColor('success')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                TextEntry::make('name')
                                    ->badge()
                                    ->color(fn (?Event $record): array => Color::generateV3Palette($record->calendar?->color)),
                                TextEntry::make('description')
                                    ->hidden(fn ($state): bool => is_null($state))
                                    ->hiddenLabel()
                                    ->html(),
                                TextEntry::make('starts')
                                    ->visible(fn (?Event $record, Get $get): bool => ! is_null($record->ends) && ! $record->repeats)
                                    ->icon(fn (?Event $record): ?string => $record->repeats ? 'heroicon-o-arrow-path' : null)
                                    ->suffix(fn (?Event $record): ?string => filled($record->length) && $record->length->total('seconds') > 0 ? sprintf(' (%s)', $record->length->forHumans(['parts' => 1])) : null)
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    })),
                                TextEntry::make('ends')
                                    ->visible(fn (?Event $record, Get $get): bool => ! is_null($record->ends) && ! $record->repeats)
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    })),
                                TextEntry::make('next_occurrence')
                                    ->label('Next Occurrence')
                                    ->visible(fn (?Event $record): bool => $record->repeats && filled($record->schedule) && filled($record->schedule->next_occurrence))
                                    ->suffix(fn (?Event $record): ?string => filled($record->schedule->length) && $record->schedule->length->total('seconds') > 0 ? sprintf(' (%s)', $record->schedule->length->forHumans(['parts' => 1])) : null)
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    })),
                                TextEntry::make('last_occurrence')
                                    ->label('Last Occurrence')
                                    ->visible(fn (?Event $record): bool => $record->repeats && filled($record->schedule) && filled($record->schedule->last_occurrence))
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    })),
                                TextEntry::make('rule_readable')
                                    ->visible(fn (?Event $record): bool => $record->repeats && filled($record->schedule))
                                    ->label('Schedule')
                                    ->getStateUsing(fn (?Event $record): ?string => ScheduleService::getSchedulePattern($record->schedule, $record->all_day)),
                            ]),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('content')
                                    ->visible(fn ($state) => filled($state))
                                    ->label('Details')
                                    ->html(),
                                TextEntry::make('location')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('url')
                                    ->visible(fn ($state) => filled($state))
                                    ->label('URL')
                                    ->url(fn (?Event $record) => $record->url),
                            ]),
                        Tab::make('Registration')
                            ->badge(fn (?Event $record) => $record->registration_enabled ? $record->registrations()->whereStatus(EventRegistrationStatus::Going)->count() : null)
                            ->badgeColor('info')
                            ->icon('heroicon-o-user-plus')
                            ->visible(fn (?Event $record) => $record->registration_enabled)
                            ->schema([
                                TextEntry::make('registration_deadline')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Deadline')
                                    ->hidden(fn (?Event $record): bool => is_null($record->registration_deadline))
                                    ->dateTime(),
                                Livewire::make(RegistrationsRelationManager::class, fn (?Event $record): array => [
                                    'ownerRecord' => $record,
                                    'pageClass' => ViewEvent::class,
                                ])->key('event-registrations'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no events to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->icon(fn (Event $record): ?string => $record->repeats ? 'heroicon-o-arrow-path' : null),
                TextColumn::make('calendar.name')
                    ->badge()
                    ->color(fn (Event $record): array => Color::generateV3Palette($record->calendar?->color)),
                TextColumn::make('author.name')
                    ->label('Organizer')
                    ->sortable(),
                IconColumn::make('all_day')
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
            ->groups(['all_day', 'repeats'])
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
}
