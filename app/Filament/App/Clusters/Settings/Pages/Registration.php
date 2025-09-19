<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Settings\RegistrationSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Checkbox;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Registration extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Registration';

    protected static string|UnitEnum|null $navigationGroup = 'Users';

    protected static string $settings = RegistrationSettings::class;

    protected static ?int $navigationSort = 7;

    protected ?string $subheading = 'Configure user registration settings.';

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
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Settings')
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
