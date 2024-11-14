<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Models\Enums\NotificationChannel;
use App\Models\Group;
use App\Models\Unit;
use App\Models\User;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

class ModelNotification
{
    public static function make(?string $description = null, bool $enabled = false, string|HtmlString|Closure|null $alert = null, ?string $defaultSubject = null, ?string $defaultMessage = null): Section
    {
        return Section::make()
            ->statePath('model_notifications')
            ->schema([
                Placeholder::make('alert')
                    ->hiddenLabel()
                    ->dehydrated(false)
                    ->visible(fn () => filled(value($alert)))
                    ->content($alert),
                Toggle::make('enabled')
                    ->live()
                    ->default($enabled)
                    ->helperText($description ?? 'Enable to send notifications.'),
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Recipients')
                            ->visible(fn (Get $get) => $get('enabled'))
                            ->icon('heroicon-o-users')
                            ->schema([
                                Select::make('groups')
                                    ->helperText('Send a notification to a group when a form us submitted.')
                                    ->preload()
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn () => Group::query()->orderBy('name')->pluck('name', 'id'))
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            if (filled($get('enabled')) && blank($get('groups')) && blank($get('units')) && blank($get('users'))) {
                                                $fail('When notifications are enabled, at least one recipient group should be selected.');
                                            }
                                        },
                                    ]),
                                Select::make('units')
                                    ->helperText('Send a notification to a unit when a form us submitted.')
                                    ->preload()
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn () => Unit::query()->orderBy('name')->pluck('name', 'id'))
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            if (filled($get('enabled')) && blank($get('groups')) && blank($get('units')) && blank($get('users'))) {
                                                $fail('When notifications are enabled, at least one recipient group should be selected.');
                                            }
                                        },
                                    ]),
                                Select::make('users')
                                    ->helperText('Send a notification to a group of users when a form us submitted.')
                                    ->preload()
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            if (filled($get('enabled')) && blank($get('groups')) && blank($get('units')) && blank($get('users'))) {
                                                $fail('When notifications are enabled, at least one recipient group should be selected.');
                                            }
                                        },
                                    ]),
                            ]),
                        Tabs\Tab::make('Notification')
                            ->visible(fn (Get $get) => $get('enabled'))
                            ->icon('heroicon-o-bell')
                            ->schema([
                                TextInput::make('subject')
                                    ->maxLength(255)
                                    ->requiredIfAccepted('enabled')
                                    ->validationMessages([
                                        'required_if_accepted' => 'The :attribute field is required when notifications are enabled.',
                                    ])
                                    ->default($defaultSubject)
                                    ->helperText('The subject to use with the notification.'),
                                RichEditor::make('message')
                                    ->maxLength(65535)
                                    ->requiredIfAccepted('enabled')
                                    ->validationMessages([
                                        'required_if_accepted' => 'The :attribute field is required when notifications are enabled.',
                                    ])
                                    ->default($defaultMessage)
                                    ->helperText('The message to use with the notification.'),
                                CheckboxList::make('channels')
                                    ->requiredIfAccepted('enabled')
                                    ->validationMessages([
                                        'required_if_accepted' => 'The :attribute field is required when notifications are enabled.',
                                    ])
                                    ->hiddenLabel()
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->descriptions(function () {
                                        return collect(NotificationChannel::cases())
                                            ->mapWithKeys(fn (NotificationChannel $channel) => [$channel->value => $channel->getDescription()])
                                            ->toArray();
                                    })
                                    ->options(function () {
                                        return collect(NotificationChannel::cases())
                                            ->filter(fn (NotificationChannel $channel) => $channel->getEnabled())
                                            ->mapWithKeys(fn (NotificationChannel $channel) => [$channel->value => $channel->getLabel()])
                                            ->toArray();
                                    }),
                            ]),
                    ]),
            ]);

    }
}
