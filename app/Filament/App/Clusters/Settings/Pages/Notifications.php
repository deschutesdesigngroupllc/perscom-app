<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Forms\Components\ModelNotification;
use App\Settings\NotificationSettings;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;

class Notifications extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Dashboard';

    protected static string $settings = NotificationSettings::class;

    protected static ?string $cluster = Settings::class;

    protected ?string $subheading = 'Manage your default notification settings.';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Assignment Records')
                            ->statePath('assignment_records')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when an assignment record is created.',
                                    alert: new HtmlString("<div class='font-bold'>Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tabs\Tab::make('Award Records')
                            ->statePath('award_records')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when an award record is created.',
                                    alert: new HtmlString("<div class='font-bold'>Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tabs\Tab::make('Combat Records')
                            ->statePath('combat_records')
                            ->icon('heroicon-o-fire')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a combat record is created.',
                                    alert: new HtmlString("<div class='font-bold'>Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tabs\Tab::make('Qualification Records')
                            ->statePath('qualification_records')
                            ->icon('heroicon-o-star')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a qualification record is created.',
                                    alert: new HtmlString("<div class='font-bold'>Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tabs\Tab::make('Rank Records')
                            ->statePath('rank_records')
                            ->icon('heroicon-o-chevron-double-up')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a rank record is created.',
                                    alert: new HtmlString("<div class='font-bold'>Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tabs\Tab::make('Service Records')
                            ->statePath('service_records')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a service record is created.',
                                    alert: new HtmlString("<div class='font-bold'>Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                    ]),
            ]);
    }
}
