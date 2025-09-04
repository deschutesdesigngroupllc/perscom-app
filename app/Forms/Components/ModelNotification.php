<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Models\Enums\NotificationChannel;
use App\Models\Group;
use App\Models\Unit;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;

class ModelNotification
{
    /**
     * @param  array<string, mixed>|null  $defaults
     */
    public static function make(?string $description = null, string|HtmlString|Closure|null $alert = null, ?array $defaults = null, ?string $statePath = null): Section
    {
        return Section::make()
            ->key('model_notifications')
            ->statePath($statePath ?? 'model_notifications')
            ->schema([
                Placeholder::make('alert')
                    ->hiddenLabel()
                    ->dehydrated(false)
                    ->visible(fn () => filled(value($alert)))
                    ->content($alert),
                Toggle::make('enabled')
                    ->live()
                    ->default(data_get($defaults, 'enabled', false))
                    ->helperText($description ?? 'Enable to send notifications.'),
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Recipients')
                            ->visible(fn (Get $get): mixed => $get('enabled'))
                            ->icon('heroicon-o-users')
                            ->schema([
                                Select::make('groups')
                                    ->helperText('Send the notification to a group(s).')
                                    ->default(data_get($defaults, 'groups'))
                                    ->preload()
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn () => Group::query()->orderBy('name')->pluck('name', 'id'))
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                            if (filled($get('enabled')) && blank($get('groups')) && blank($get('units')) && blank($get('users'))) {
                                                $fail('When notifications are enabled, at least one recipient group should be selected.');
                                            }
                                        },
                                    ]),
                                Select::make('units')
                                    ->helperText('Send the notification to a unit(s).')
                                    ->default(data_get($defaults, 'units'))
                                    ->preload()
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn () => Unit::query()->orderBy('name')->pluck('name', 'id'))
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                            if (filled($get('enabled')) && blank($get('groups')) && blank($get('units')) && blank($get('users'))) {
                                                $fail('When notifications are enabled, at least one recipient group should be selected.');
                                            }
                                        },
                                    ]),
                                Select::make('users')
                                    ->helperText('Send the notification to a user(s).')
                                    ->default(data_get($defaults, 'users'))
                                    ->preload()
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                            if (filled($get('enabled')) && blank($get('groups')) && blank($get('units')) && blank($get('users'))) {
                                                $fail('When notifications are enabled, at least one recipient group should be selected.');
                                            }
                                        },
                                    ]),
                            ]),
                        Tab::make('Notification')
                            ->visible(fn (Get $get): mixed => $get('enabled'))
                            ->icon('heroicon-o-bell')
                            ->schema([
                                TextInput::make('subject')
                                    ->default(data_get($defaults, 'subject'))
                                    ->hintIconTooltip('View available content tags.')
                                    ->hint('Content Tags')
                                    ->hintColor('gray')
                                    ->hintIcon('heroicon-o-tag')
                                    ->hintAction(Action::make('view')
                                        ->color('gray')
                                        ->modalHeading('Content Tags')
                                        ->modalContent(view('app.model-tags'))
                                        ->modalSubmitAction(false)
                                        ->modalCancelActionLabel('Close')
                                        ->modalDescription('Content tags provide a way for you to dynamically insert data into a body of text. The tags will be replaced with relevant data from whatever resource the content is attached to.')
                                        ->slideOver())
                                    ->maxLength(255)
                                    ->requiredIfAccepted('enabled')
                                    ->validationMessages([
                                        'required_if_accepted' => 'The :attribute field is required when notifications are enabled.',
                                    ])
                                    ->helperText('The subject to use with the notification.'),
                                RichEditor::make('message')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->default(data_get($defaults, 'message'))
                                    ->hintIconTooltip('View available content tags.')
                                    ->hint('Content Tags')
                                    ->hintColor('gray')
                                    ->hintIcon('heroicon-o-tag')
                                    ->hintAction(Action::make('view')
                                        ->color('gray')
                                        ->modalHeading('Content Tags')
                                        ->modalContent(view('app.model-tags'))
                                        ->modalSubmitAction(false)
                                        ->modalCancelActionLabel('Close')
                                        ->modalDescription('Content tags provide a way for you to dynamically insert data into a body of text. The tags will be replaced with relevant data from whatever resource the content is attached to.')
                                        ->slideOver())
                                    ->maxLength(65535)
                                    ->requiredIfAccepted('enabled')
                                    ->validationMessages([
                                        'required_if_accepted' => 'The :attribute field is required when notifications are enabled.',
                                    ])
                                    ->helperText('The message to use with the notification.'),
                                CheckboxList::make('channels')
                                    ->helperText('The channels the notification will be sent to.')
                                    ->default(data_get($defaults, 'channels'))
                                    ->requiredIfAccepted('enabled')
                                    ->validationMessages([
                                        'required_if_accepted' => 'The :attribute field is required when notifications are enabled.',
                                    ])
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->descriptions(fn () => collect(NotificationChannel::cases())
                                        ->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getDescription()])
                                        ->toArray())
                                    ->options(fn () => collect(NotificationChannel::cases())
                                        ->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled())
                                        ->mapWithKeys(fn (NotificationChannel $channel): array => [$channel->value => $channel->getLabel()])
                                        ->toArray()),
                            ]),
                    ]),
            ]);

    }
}
