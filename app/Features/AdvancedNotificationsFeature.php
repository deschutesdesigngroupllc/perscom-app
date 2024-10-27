<?php

declare(strict_types=1);

namespace App\Features;

use App\Contracts\PremiumFeature;
use App\Services\DiscordService;
use App\Services\TwilioService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\Facades\App;
use Laravel\Pennant\Feature;
use Spark\Plan;

use function app;

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
                            Select::make('discord_server')
                                ->suffixAction(function () {
                                    return Action::make('add_to_server')
                                        ->openUrlInNewTab()
                                        ->url(DiscordService::addBotToServerLink())
                                        ->icon('heroicon-o-plus-circle')
                                        ->label('Add To Server');
                                })
                                ->searchable()
                                ->preload()
                                ->live(onBlur: true)
                                ->label('Server')
                                ->options(function () {
                                    return collect(DiscordService::getGuilds())->mapWithKeys(function ($data) {
                                        return [data_get($data, 'id') => data_get($data, 'name')];
                                    });
                                })
                                ->helperText(function () {
                                    return 'Use the button to the right to add the PERSCOM bot to your community Discord server to send automated notifications for everyone to see.';
                                }),
                            Select::make('discord_channel')
                                ->label('Channel')
                                ->searchable()
                                ->preload()
                                ->options(function (Get $get) {
                                    $guildId = $get('discord_server');

                                    if (blank($guildId)) {
                                        return [];
                                    }

                                    return collect(DiscordService::getChannels($guildId))
                                        ->filter(fn ($data) => (string) data_get($data, 'type') === '0')
                                        ->mapWithKeys(function ($data) {
                                            return [data_get($data, 'id') => data_get($data, 'name')];
                                        });
                                })
                                ->helperText('Select the channel the notifications will be posted to.'),
                        ]),
                    Tab::make('SMS')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->schema([
                            Toggle::make('sms_enabled')
                                ->helperText('Enable SMS notifications system wide.')
                                ->label('Enabled'),
                            Placeholder::make('attempts')
                                ->label('Daily SMS Limit')
                                ->helperText('Each account is limited to a daily limit of SMS text messages. To increase your rate, please reach out to support.')
                                ->content(function (?\App\Models\Feature $record) {
                                    /** @var TwilioService $service */
                                    $service = app(TwilioService::class);

                                    $attempts = $service->limiter->attempts('sms') ?? 0;

                                    return "$attempts / 50";
                                }),
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
