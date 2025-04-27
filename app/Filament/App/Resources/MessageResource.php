<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MessageResource\Pages;
use App\Forms\Components\Schedule;
use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\User;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessageResource extends BaseResource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Communications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Message')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema(static::messageSchema()),
                        Forms\Components\Tabs\Tab::make('Channels')
                            ->icon('heroicon-o-queue-list')
                            ->schema(static::channelSchema()),
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema(static::detailsSchema()),
                        Forms\Components\Tabs\Tab::make('Schedule')
                            ->visible(fn (Forms\Get $get): mixed => $get('repeats'))
                            ->icon('heroicon-o-arrow-path')
                            ->schema(static::scheduleSchema()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->icon(fn (Message $record): ?string => $record->repeats ? 'heroicon-o-arrow-path' : null),
                Tables\Columns\TextColumn::make('channels')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('send_at')
                    ->label('Send')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['repeats'])
            ->filters([
                Tables\Filters\SelectFilter::make('channels')
                    ->options(NotificationChannel::class)
                    ->modifyQueryUsing(fn (Builder $query, $data) => $query->when(! is_null(data_get($data, 'value')))->whereJsonContains('channels', data_get($data, 'value'))),
                Tables\Filters\TernaryFilter::make('repeats'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
        ];
    }

    public static function messageSchema(): array
    {
        return [
            Forms\Components\RichEditor::make('message')
                ->helperText('Enter the message you would like to send.')
                ->required()
                ->hiddenLabel(),
        ];
    }

    public static function channelSchema(): array
    {
        return [
            Forms\Components\CheckboxList::make('channels')
                ->required()
                ->searchable()
                ->hiddenLabel()
                ->bulkToggleable()
                ->descriptions(fn () => collect(NotificationChannel::cases())
                    ->mapWithKeys(fn (NotificationChannel $channel) => [$channel->value => $channel->getDescription()])
                    ->toArray())
                ->options(fn () => collect(NotificationChannel::cases())
                    ->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled())
                    ->mapWithKeys(fn (NotificationChannel $channel) => [$channel->value => $channel->getLabel()])
                    ->toArray()),
        ];
    }

    public static function detailsSchema(): array
    {
        return [
            Forms\Components\Select::make('recipients')
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
            Forms\Components\DateTimePicker::make('send_at')
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
                ->hidden(fn (Forms\Get $get): mixed => $get('repeats')),
            Forms\Components\Toggle::make('repeats')
                ->live()
                ->helperText('Enable to send the message on a recurring schedule.'),
            Schedule::make(shiftScheduleTimezone: true)
                ->visible(fn (Forms\Get $get): mixed => $get('repeats')),
        ];
    }
}
