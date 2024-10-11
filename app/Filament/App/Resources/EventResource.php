<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\EventResource\Pages;
use App\Filament\App\Resources\EventResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\EventResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Filament\Exports\EventExporter;
use App\Forms\Components\Schedule;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Services\RepeatService;
use App\Services\UserSettingsService;
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
                                Forms\Components\DateTimePicker::make('starts')
                                    ->helperText('The date and time the event starts.')
                                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                                    ->default(now()->addHour()->startOfHour())
                                    ->live(onBlur: true)
                                    ->required()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state, $component) {
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
                                    ->time(fn (Forms\Get $get) => ! $get('all_day')),
                                Forms\Components\DateTimePicker::make('ends')
                                    ->helperText('The date and time the event ends.')
                                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                                    ->default(now()->addHours(2)->startOfHour())
                                    ->afterOrEqual('starts')
                                    ->live(onBlur: true)
                                    ->required()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state, $component) {
                                        $start = Carbon::parse($get('starts'))
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $end = Carbon::parse($state)
                                            ->shiftTimezone($component->getTimezone())
                                            ->setTimezone(config('app.timezone'));

                                        $set('schedule.duration', $start->diffInHours($end));
                                    })
                                    ->time(fn (Forms\Get $get) => ! $get('all_day')),
                                Forms\Components\Toggle::make('all_day')
                                    ->helperText('Check if the event does not have a start and end time.')
                                    ->columnSpanFull()
                                    ->live()
                                    ->required(),
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
                                    ->label('URL')
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
                                    ->hiddenLabel()
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
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Forms\Components\Toggle::make('notifications_enabled')
                                    ->live()
                                    ->label('Enabled')
                                    ->validationAttribute('enabled notifications')
                                    ->default(false)
                                    ->helperText('Send reminder notifications to all registered users.'),
                                Forms\Components\Select::make('notifications_interval')
                                    ->visible(fn (Forms\Get $get) => $get('notifications_enabled'))
                                    ->label('Alert')
                                    ->helperText('When should the notifications be sent.')
                                    ->requiredIf('notifications_enabled', true)
                                    ->multiple()
                                    ->options(NotificationInterval::class),
                                Forms\Components\CheckboxList::make('notifications_channels')
                                    ->visible(fn (Forms\Get $get) => $get('notifications_enabled'))
                                    ->label('Channels')
                                    ->requiredIf('notifications_enabled', true)
                                    ->bulkToggleable()
                                    ->descriptions(function () {
                                        return collect(NotificationChannel::cases())->mapWithKeys(function (NotificationChannel $case) {
                                            return [$case->value => $case->getDescription()];
                                        })->toArray();
                                    })
                                    ->options(function () {
                                        return collect(NotificationChannel::cases())->filter(function (NotificationChannel $channel) {
                                            return $channel->getEnabled();
                                        })->mapWithKeys(function (NotificationChannel $case) {
                                            return [$case->value => $case->getLabel()];
                                        })->toArray();
                                    }),
                            ]),
                        Forms\Components\Tabs\Tab::make('Schedule')
                            ->icon('heroicon-o-arrow-path')
                            ->columns()
                            ->schema(function (Forms\Get $get) {
                                return [
                                    Forms\Components\Toggle::make('repeats')
                                        ->helperText('Check if the event repeats.')
                                        ->columnSpanFull()
                                        ->default(false)
                                        ->live(),
                                    Schedule::make(
                                        startHidden: true,
                                        allDay: $get('all_day') ?? false
                                    )->visible($get('repeats') ?? false),
                                ];
                            }),
                        Forms\Components\Tabs\Tab::make('Registration')
                            ->icon('heroicon-o-user-plus')
                            ->schema([
                                Forms\Components\Toggle::make('registration_enabled')
                                    ->live()
                                    ->label('Enabled')
                                    ->helperText('Whether registration is enabled for the event.')
                                    ->default(false),
                                Forms\Components\DateTimePicker::make('registration_deadline')
                                    ->visible(fn (Forms\Get $get) => $get('registration_enabled'))
                                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                                    ->label('Deadline')
                                    ->nullable()
                                    ->helperText('The deadline for registration. Leave blank for no deadline.'),
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
                                TextEntry::make('starts')
                                    ->visible(fn (?Event $record, Forms\Get $get) => ! is_null($record->ends) && ! $record->repeats)
                                    ->icon(fn (?Event $record) => $record->repeats ? 'heroicon-o-arrow-path' : null)
                                    ->suffix(fn (?Event $record) => filled($record->length) && $record->length->total('seconds') > 0 ? " ({$record->length->forHumans(['parts' => 1])})" : null)
                                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                                    ->getStateUsing(function (?Event $record, $component) {
                                        return match ($record->all_day) {
                                            true => Carbon::parse($record->starts)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateDisplayFormat),
                                            false => Carbon::parse($record->starts)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateTimeDisplayFormat)
                                        };
                                    }),
                                TextEntry::make('ends')
                                    ->visible(fn (?Event $record, Forms\Get $get) => ! is_null($record->ends) && ! $record->repeats)
                                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                                    ->getStateUsing(function (?Event $record, TextEntry $component) {
                                        return match ($record->all_day) {
                                            true => Carbon::parse($record->ends)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateDisplayFormat),
                                            false => Carbon::parse($record->ends)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateTimeDisplayFormat)
                                        };
                                    }),
                                TextEntry::make('next_occurrence')
                                    ->label('Next Occurrence')
                                    ->visible(fn (?Event $record) => $record->repeats && filled($record->schedule) && filled($record->schedule->next_occurrence))
                                    ->suffix(fn (?Event $record) => filled($record->schedule->length) && $record->schedule->length->total('seconds') > 0 ? " ({$record->schedule->length->forHumans(['parts' => 1])})" : null)
                                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                                    ->getStateUsing(function (?Event $record, TextEntry $component) {
                                        return match ($record->all_day) {
                                            true => Carbon::parse($record->schedule->next_occurrence)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateDisplayFormat),
                                            false => Carbon::parse($record->schedule->next_occurrence)->setTimezone($component->getTimezone())->translatedFormat(Infolist::$defaultDateTimeDisplayFormat)
                                        };
                                    }),
                                TextEntry::make('rule_readable')
                                    ->visible(fn (?Event $record) => $record->repeats && filled($record->schedule))
                                    ->label('Schedule')
                                    ->getStateUsing(fn (?Event $record) => RepeatService::getSchedulePattern($record->schedule, $record->all_day)),
                            ]),
                        Tabs\Tab::make('Details')
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
                        Tabs\Tab::make('Registration')
                            ->badge(fn (?Event $record) => $record->registration_enabled ? $record->registrations()->count() : null)
                            ->badgeColor('info')
                            ->icon('heroicon-o-user-plus')
                            ->visible(fn (?Event $record) => $record->registration_enabled)
                            ->schema([
                                TextEntry::make('registration_deadline')
                                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
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
                    ->icon(fn (Event $record) => $record->repeats ? 'heroicon-o-arrow-path' : null),
                Tables\Columns\TextColumn::make('calendar.name')
                    ->badge()
                    ->color(fn (Event $record) => Color::hex($record->calendar?->color)),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Organizer')
                    ->sortable(),
                Tables\Columns\IconColumn::make('all_day')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->timezone(UserSettingsService::get('timezone', config('app.timezone')))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordClasses(fn (Event $record) => match ($record->has_passed) {
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
