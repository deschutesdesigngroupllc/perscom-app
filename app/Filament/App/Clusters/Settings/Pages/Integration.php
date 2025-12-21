<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Filament\App\Resources\PassportClientResource;
use App\Filament\App\Resources\PassportTokenResource;
use App\Filament\App\Resources\WebhookResource;
use App\Services\DiscordService;
use App\Services\TwilioService;
use App\Settings\IntegrationSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Integration extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Integrations';

    protected static ?int $navigationSort = 2;

    protected static string $settings = IntegrationSettings::class;

    protected ?string $subheading = 'Adjust settings related to integrating PERSCOM with third-party services.';

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
                        Tab::make('Discord')
                            ->icon('fab-discord')
                            ->schema([
                                Toggle::make('discord_settings.discord_enabled')
                                    ->live()
                                    ->helperText('Enable Discord notifications system wide.')
                                    ->label('Enabled'),
                                Select::make('discord_settings.discord_server')
                                    ->visible(fn (Get $get): bool => $get('discord_settings.discord_enabled'))
                                    ->suffixAction(fn (): Action => Action::make('add_to_server')
                                        ->openUrlInNewTab()
                                        ->url(DiscordService::addBotToServerLink())
                                        ->icon('heroicon-o-plus-circle')
                                        ->label('Add To Server'))
                                    ->searchable()
                                    ->preload()
                                    ->live(onBlur: true)
                                    ->label('Server')
                                    ->options(fn () => collect(DiscordService::getGuilds())->mapWithKeys(fn ($data): array => [data_get($data, 'id') => data_get($data, 'name')]))
                                    ->helperText(fn (): string => 'Use the button to the right to add the PERSCOM bot to your community Discord server to send automated notifications for everyone to see.'),
                                Select::make('discord_settings.discord_channel')
                                    ->visible(fn (Get $get): bool => $get('discord_settings.discord_enabled'))
                                    ->label('Channel')
                                    ->searchable()
                                    ->preload()
                                    ->options(function (Get $get) {
                                        $guildId = $get('discord_settings.discord_server');

                                        if (blank($guildId)) {
                                            return [];
                                        }

                                        return collect(DiscordService::getChannels($guildId))
                                            ->filter(fn ($data): bool => (string) data_get($data, 'type') === '0')
                                            ->mapWithKeys(fn ($data): array => [data_get($data, 'id') => data_get($data, 'name')]);
                                    })
                                    ->helperText('Select the channel the notifications will be posted to.'),
                            ]),
                        Tab::make('Single Sign-On (SSO)')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('single_sign_on_key')
                                    ->label('SSO Key')
                                    ->disabled()
                                    ->helperText('Use this Single Sign-On Key to sign JWT access tokens and access PERSCOM.resources on the fly through the PERSCOM API.')
                                    ->suffixAction(fn (): Action => Action::make('regenerate')
                                        ->icon('heroicon-o-arrow-path')
                                        ->successNotificationTitle('The SSO key has been successfully regenerated.')
                                        ->action(function (Action $action): void {
                                            $settings = app(IntegrationSettings::class);
                                            $settings->single_sign_on_key = Str::random(40);
                                            $settings->save();

                                            $action->success();

                                            $this->fillForm();
                                        })),
                            ]),
                        Tab::make('SMS')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Toggle::make('sms_settings.sms_enabled')
                                    ->live()
                                    ->helperText("Enable SMS notifications system wide. Text messages will be sent to a user's phone number if they have one on file.")
                                    ->label('Enabled'),
                                Placeholder::make('attempts')
                                    ->visible(fn (Get $get): bool => $get('sms_settings.sms_enabled'))
                                    ->label('Daily SMS Limit')
                                    ->helperText('Each account is limited to a daily limit of SMS text messages. To increase your rate, please reach out to support.')
                                    ->content(function (): string {
                                        /** @var TwilioService $service */
                                        $service = app(TwilioService::class);

                                        $attempts = $service->limiter->attempts('sms') ?? 0;

                                        return $attempts.' / 50';
                                    }),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('API Keys')
                ->label('API Keys')
                ->color('gray')
                ->url(PassportTokenResource::getUrl()),
            Action::make('OAuth 2.0 Clients')
                ->label('OAuth 2.0 Clients')
                ->color('gray')
                ->url(PassportClientResource::getUrl()),
            Action::make('Webhooks')
                ->label('Webhooks')
                ->color('gray')
                ->url(WebhookResource::getUrl()),
        ];
    }
}
