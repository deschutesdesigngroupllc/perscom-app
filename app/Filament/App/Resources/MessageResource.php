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
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
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
                    ->persistTabInQueryString()
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
            ->emptyStateIcon(Heroicon::OutlinedChatBubbleLeftRight)
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
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('send_at')
                    ->placeholder('Sent Immediately')
                    ->label('Send')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->placeholder('Not Sent Yet')
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
                ViewAction::make()
                    ->modalDescription('View the message details.'),
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
     * @return Component[]
     */
    public static function messageSchema(): array
    {
        return [
            Text::make('tip')
                ->visibleOn('create')
                ->columnSpanFull()
                ->content(fn (): HtmlString => new HtmlString(
                    '<div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">'.
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-warning shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>'.
                    '<span><strong>Did you know?</strong> You can send this message automatically using <a href="'.AutomationResource::getUrl('create').'" class="font-medium text-primary-600 hover:underline dark:text-primary-400">Automations</a>.</span>'.
                    '</div>'
                )),
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
                ->label('Send At')
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
