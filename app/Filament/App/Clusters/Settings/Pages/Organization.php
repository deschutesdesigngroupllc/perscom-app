<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Settings\OrganizationSettings as OrganizationSettingsClass;
use BezhanSalleh\FilamentShield\Support\Utils;
use DateTimeZone;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Organization extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Organization';

    protected static string $settings = OrganizationSettingsClass::class;

    protected static ?int $navigationSort = 4;

    protected ?string $subheading = 'Account related settings specific to your organizational details.';

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Account')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                TextInput::make('name')
                                    ->maxLength(255)
                                    ->required()
                                    ->helperText('The name of the organization.'),
                                TextInput::make('email')
                                    ->maxLength(255)
                                    ->email()
                                    ->required()
                                    ->helperText('The organization email that will receive all account-related emails.'),
                                Select::make('timezone')
                                    ->searchable()
                                    ->preload()
                                    ->options(collect(DateTimeZone::listIdentifiers())->mapWithKeys(fn ($timezone) => [$timezone => $timezone])->all())
                                    ->required()
                                    ->helperText('The timezone that will be used for all dates and times. Note: A user can still override the system timezone on a per-account basis.'),
                            ]),
                    ]),
            ]);
    }
}
