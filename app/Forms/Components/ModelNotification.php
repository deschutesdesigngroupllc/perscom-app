<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Models\Enums\NotificationChannel;
use App\Models\Group;
use App\Models\Unit;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

class ModelNotification
{
    public static function make(?string $description = null): Section
    {
        return Section::make()
            ->statePath('model_notifications')
            ->schema([
                Toggle::make('enabled')
                    ->live()
                    ->helperText($description ?? 'Enable to send notifications.'),
                Section::make('Recipients')
                    ->visible(fn (Get $get) => $get('enabled'))
                    ->description('Who the notifications will be sent to.')
                    ->schema([
                        Select::make('groups')
                            ->helperText('Send a notification to a group when a form us submitted.')
                            ->preload()
                            ->multiple()
                            ->searchable()
                            ->options(fn () => Group::query()->orderBy('name')->pluck('name', 'id')),
                        Select::make('units')
                            ->helperText('Send a notification to a unit when a form us submitted.')
                            ->preload()
                            ->multiple()
                            ->searchable()
                            ->options(fn () => Unit::query()->orderBy('name')->pluck('name', 'id')),
                        Select::make('users')
                            ->helperText('Send a notification to a group of users when a form us submitted.')
                            ->preload()
                            ->multiple()
                            ->searchable()
                            ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id')),
                    ]),
                Section::make('Notification')
                    ->visible(fn (Get $get) => $get('enabled'))
                    ->description('Configure the notification to be sent.')
                    ->schema([
                        TextInput::make('subject')
                            ->maxLength(255)
                            ->requiredWith(['groups', 'units', 'users'])
                            ->helperText('The subject to use with the notification.'),
                        RichEditor::make('message')
                            ->maxLength(65535)
                            ->requiredWith(['groups', 'units', 'users'])
                            ->helperText('The message to use with the notification.'),
                        CheckboxList::make('channels')
                            ->requiredWith(['groups', 'units', 'users'])
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
            ]);

    }
}
