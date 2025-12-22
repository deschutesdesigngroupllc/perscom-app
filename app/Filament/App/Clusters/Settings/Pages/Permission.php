<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Models\Permission as PermissionModel;
use App\Models\Role;
use App\Settings\PermissionSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Permission extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Permissions';

    protected static string|UnitEnum|null $navigationGroup = 'Users';

    protected static string $settings = PermissionSettings::class;

    protected static ?int $navigationSort = 3;

    protected ?string $subheading = 'Adjust and configure authorization for your account.';

    protected static ?string $slug = 'users/permissions';

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
                        Tab::make('Defaults')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Select::make('default_roles')
                                    ->label('Role(s)')
                                    ->searchable()
                                    ->multiple()
                                    ->options(Role::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                    ->dehydrateStateUsing(fn ($state) => collect($state)->map(fn ($id): int => (int) $id)->toArray())
                                    ->helperText('The default role(s) a new user will be granted when created.'),
                                Select::make('default_permissions')
                                    ->label('Permission(s)')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->options(PermissionModel::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                    ->dehydrateStateUsing(fn ($state) => collect($state)->map(fn ($id): int => (int) $id)->toArray())
                                    ->helperText('The default permission(s) a new user will be granted when created.'),
                            ]),
                    ]),
            ]);
    }
}
