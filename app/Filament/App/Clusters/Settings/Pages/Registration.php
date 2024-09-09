<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Settings\RegistrationSettings as RegistrationSettingsClass;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Registration extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Registration';

    protected static string $settings = RegistrationSettingsClass::class;

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
                        Tabs\Tab::make('Settings')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Checkbox::make('enabled')
                                    ->default(true)
                                    ->helperText('Uncheck to disable user registration.'),
                                Checkbox::make('admin_approval_required')
                                    ->label('Admin Approval Required')
                                    ->default(false)
                                    ->helperText('Check to require admin approval before a user account can login once registered.'),
                            ]),
                    ]),
            ]);
    }
}
