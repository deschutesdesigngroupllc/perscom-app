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
use Illuminate\Support\Facades\Auth;

class Organization extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Organization';

    protected static string $settings = OrganizationSettingsClass::class;

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole(Utils::getSuperAdminName());
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
                                    ->required()
                                    ->helperText('The name of the organization.'),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->helperText('The organization email that will receive all account-related emails.'),
                                Select::make('timezone')
                                    ->options(collect(DateTimeZone::listIdentifiers())->mapWithKeys(fn ($timezone) => [$timezone => $timezone])->all())
                                    ->required()
                                    ->helperText('The timezone that will be used for all dates and times.'),
                            ]),
                    ]),
            ]);
    }
}
