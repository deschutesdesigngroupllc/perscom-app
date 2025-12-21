<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Forms\Components\ModelNotification;
use App\Settings\NotificationSettings;
use BackedEnum;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use UnitEnum;

class Notifications extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static string|UnitEnum|null $navigationGroup = 'Dashboard';

    protected static string $settings = NotificationSettings::class;

    protected static ?string $cluster = Settings::class;

    protected ?string $subheading = 'Manage your default notification settings.';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'dashboard/notifications';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Assignment Records')
                            ->statePath('assignment_records')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when an assignment record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Award Records')
                            ->statePath('award_records')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when an award record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Combat Records')
                            ->statePath('combat_records')
                            ->icon('heroicon-o-fire')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a combat record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Qualification Records')
                            ->statePath('qualification_records')
                            ->icon('heroicon-o-star')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a qualification record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Rank Records')
                            ->statePath('rank_records')
                            ->icon('heroicon-o-chevron-double-up')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a rank record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Service Records')
                            ->statePath('service_records')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a service record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Training Records')
                            ->statePath('training_records')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a training record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                    ]),
            ]);
    }
}
