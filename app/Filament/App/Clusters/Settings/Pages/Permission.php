<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Models\Permission as PermissionModel;
use App\Models\Role;
use App\Settings\PermissionSettings as PermissionSettingsClass;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Permission extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Permissions';

    protected static string $settings = PermissionSettingsClass::class;

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
                        Tabs\Tab::make('Defaults')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Select::make('default_roles')
                                    ->label('Role(s)')
                                    ->searchable()
                                    ->multiple()
                                    ->options(Role::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                    ->helperText('The default role(s) a new user will be granted when created.'),
                                Select::make('default_permissions')
                                    ->label('Permission(s)')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->options(PermissionModel::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                    ->helperText('The default permission(s) a new user will be granted when created.'),
                            ]),
                    ]),
            ]);
    }
}
