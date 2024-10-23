<?php

declare(strict_types=1);

namespace App\Features;

use App\Contracts\PremiumFeature;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\App;
use Laravel\Pennant\Feature;
use Spark\Plan;

class AdvancedNotificationsFeature extends BaseFeature implements PremiumFeature
{
    public static function canSubscribe(): bool
    {
        $tenant = static::resolveTenant();

        if (! $tenant) {
            return false;
        }

        return optional($tenant->sparkPlan())->name !== 'Enterprise'
            && Feature::inactive(static::class);
    }

    public static function canUnsubscribe(): bool
    {
        $tenant = static::resolveTenant();

        if (! $tenant) {
            return false;
        }

        return optional($tenant->sparkPlan())->name !== 'Enterprise'
            && Feature::active(static::class);
    }

    public static function settingsKey(): string
    {
        return 'advanced_notifications';
    }

    public static function settingsIcon(): string
    {
        return 'heroicon-o-bell-alert';
    }

    /**
     * @return array<Component>
     */
    public static function settingsForm(): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make('Discord')
                        ->icon('fab-discord')
                        ->schema([
                            Toggle::make('discord_enabled')
                                ->helperText('Enable Discord notifications system wide.')
                                ->label('Enabled'),
                        ]),
                    Tab::make('SMS')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->schema([
                            Toggle::make('sms_enabled')
                                ->helperText('Enable SMS notifications system wide.')
                                ->label('Enabled'),
                        ]),
                ]),
        ];
    }

    public function resolve(?string $scope): bool
    {
        $tenant = static::resolveTenant($scope);
        $premiumFeatures = config('spark.premium_features');

        return match (true) {
            App::isAdmin() => false,
            App::isDemo() => false,
            $tenant->onTrial() => false,
            $tenant->subscribedToPrice(data_get($premiumFeatures, static::class)) => true,
            optional($tenant->sparkPlan(), static function (Plan $plan) {
                return in_array(__CLASS__, $plan->options, true);
            }) === true => true,
            default => false,
        };
    }
}
