<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Forms\Components\ModelNotification;
use App\Settings\NotificationSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use UnitEnum;

class Notifications extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static string|UnitEnum|null $navigationGroup = 'General';

    protected static string $settings = NotificationSettings::class;

    protected static ?string $cluster = Settings::class;

    protected ?string $subheading = 'Manage your default notification settings.';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'dashboard/notifications';

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }

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
                            ->icon(Heroicon::OutlinedRectangleStack)
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when an assignment record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Award Records')
                            ->statePath('award_records')
                            ->icon(Heroicon::OutlinedTrophy)
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when an award record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Combat Records')
                            ->statePath('combat_records')
                            ->icon(Heroicon::OutlinedFire)
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a combat record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Qualification Records')
                            ->statePath('qualification_records')
                            ->icon(Heroicon::OutlinedStar)
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a qualification record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Rank Records')
                            ->statePath('rank_records')
                            ->icon(Heroicon::OutlinedChevronDoubleUp)
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a rank record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Service Records')
                            ->statePath('service_records')
                            ->icon(Heroicon::OutlinedClipboardDocumentList)
                            ->schema([
                                ModelNotification::make(
                                    description: 'Enable to send additional notifications when a service record is created.',
                                    alert: new HtmlString("<div class='fi-sc-text'>Configure the default notifications to send when creating a record. Notifications will still be sent to the recipient of the record regardless of the settings below.</div>"),
                                    statePath: ''
                                ),
                            ]),
                        Tab::make('Training Records')
                            ->statePath('training_records')
                            ->icon(Heroicon::OutlinedAcademicCap)
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
