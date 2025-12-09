<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MessageResource\Pages\CreateMessage;
use App\Filament\App\Resources\MessageResource\Pages\ListMessages;
use App\Forms\Components\Schedule;
use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\User;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MessageResource extends BaseResource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|UnitEnum|null $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Message')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema(static::messageSchema()),
                        Tab::make('Channels')
                            ->icon('heroicon-o-queue-list')
                            ->schema(static::channelSchema()),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema(static::detailsSchema()),
                        Tab::make('Schedule')
                            ->visible(fn (Get $get): mixed => $get('repeats'))
                            ->icon('heroicon-o-arrow-path')
                            ->schema(static::scheduleSchema()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no messages to view. Create one to get started.')
            ->columns([
                TextColumn::make('message')
                    ->searchable()
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->icon(fn (Message $record): ?string => $record->repeats ? 'heroicon-o-arrow-path' : null),
                TextColumn::make('channels')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('send_at')
                    ->label('Send')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->label('Sent')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['repeats'])
            ->filters([
                SelectFilter::make('channels')
                    ->options(NotificationChannel::class)
                    ->modifyQueryUsing(fn (Builder $query, $data) => $query->when(! is_null(data_get($data, 'value')))->whereJsonContains('channels', data_get($data, 'value'))),
                TernaryFilter::make('repeats'),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListMessages::route('/'),
            'create' => CreateMessage::route('/create'),
        ];
    }

    /**
     * @return RichEditor[]
     */
    public static function messageSchema(): array
    {
        return [
            RichEditor::make('message')
                ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                ->helperText('Enter the message you would like to send.')
                ->required()
                ->hiddenLabel(),
        ];
    }

    /**
     * @return CheckboxList[]
     */
    public static function channelSchema(): array
    {
        return [
            CheckboxList::make('channels')
                ->required()
                ->searchable()
                ->hiddenLabel()
                ->bulkToggleable()
                ->descriptions(fn () => collect(NotificationChannel::cases())
                    ->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getDescription()])
                    ->toArray())
                ->options(fn () => collect(NotificationChannel::cases())
                    ->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled())
                    ->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getLabel()])
                    ->toArray()),
        ];
    }

    /**
     * @return Select[]
     */
    public static function detailsSchema(): array
    {
        return [
            Select::make('recipients')
                ->helperText('Select the recipients. Leave blank to send to everyone.')
                ->preload()
                ->multiple()
                ->searchable()
                ->nullable()
                ->options(User::query()->orderBy('name')->pluck('name', 'id')),
        ];
    }

    public static function scheduleSchema(): array
    {
        return [
            DateTimePicker::make('send_at')
                ->timezone(UserSettingsService::get('timezone', function () {
                    /** @var OrganizationSettings $settings */
                    $settings = app(OrganizationSettings::class);

                    return $settings->timezone ?? config('app.timezone');
                }))
                ->minDate(fn ($component) => now()
                    ->subDay()
                    ->shiftTimezone($component->getTimezone())
                    ->setTimezone(config('app.timezone')))
                ->columnSpanFull()
                ->helperText('Set a time to send the message in the future. Leave blank to send now.')
                ->hidden(fn (Get $get): mixed => $get('repeats')),
            Toggle::make('repeats')
                ->live()
                ->helperText('Enable to send the message on a recurring schedule.'),
            Schedule::make(shiftScheduleTimezone: true)
                ->visible(fn (Get $get): mixed => $get('repeats')),
        ];
    }
}
