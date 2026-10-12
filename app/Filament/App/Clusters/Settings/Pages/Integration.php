<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Actions\Integrations\DisconnectDiscordServer;
use App\Actions\Integrations\DisconnectSlackWorkspace;
use App\Filament\App\Clusters\Settings;
use App\Filament\App\Resources\PassportClientResource;
use App\Filament\App\Resources\PassportTokenResource;
use App\Filament\App\Resources\WebhookResource;
use App\Jobs\Tenant\SyncDiscordMember;
use App\Models\User;
use App\Services\DiscordService;
use App\Services\SlackService;
use App\Services\TwilioService;
use App\Settings\IntegrationSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class Integration extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string|UnitEnum|null $navigationGroup = 'AI & Integrations';

    protected static ?string $navigationLabel = 'Integrations';

    protected static ?int $navigationSort = 12;

    protected static string $settings = IntegrationSettings::class;

    protected ?string $subheading = 'Adjust settings related to integrating PERSCOM with third-party services.';

    protected bool $shouldSyncDiscordMembers = false;

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
                        Tab::make('Discord')
                            ->icon('fab-discord')
                            ->visible(fn (): bool => filled(config('services.discord.client_id')) && filled(config('services.discord.client_secret')))
                            ->schema([
                                TextEntry::make('discord_connection')
                                    ->label('Server')
                                    ->state(fn (): string => $this->discordServerName() ?? 'Not connected')
                                    ->badge()
                                    ->color(fn (): string => filled($this->discordServerName()) ? 'success' : 'gray')
                                    ->icon(fn (): string => filled($this->discordServerName()) ? 'heroicon-m-check-circle' : 'heroicon-m-minus-circle')
                                    ->helperText('Connecting adds the PERSCOM bot to your community server. You will need the Manage Server permission in Discord.')
                                    ->afterContent([
                                        Action::make('connectDiscord')
                                            ->label(fn (): string => filled($this->discordServerName()) ? 'Change Server' : 'Connect Server')
                                            ->icon('fab-discord')
                                            ->color(fn (): string => filled($this->discordServerName()) ? 'gray' : 'primary')
                                            ->url(fn (): string => route('app.integrations.install', ['provider' => 'discord'])),
                                        Action::make('disconnectDiscord')
                                            ->label('Disconnect')
                                            ->color('danger')
                                            ->link()
                                            ->visible(fn (): bool => filled($this->discordServerName()))
                                            ->requiresConfirmation()
                                            ->modalHeading('Disconnect Discord')
                                            ->modalDescription('The PERSCOM bot will leave your server and Discord notifications, commands and role sync will stop.')
                                            ->action(function (): void {
                                                DisconnectDiscordServer::handle();

                                                $this->fillForm();
                                            }),
                                    ]),
                                Toggle::make('discord_settings.discord_enabled')
                                    ->live()
                                    ->visible(fn (): bool => filled($this->discordServerName()))
                                    ->helperText('Enable Discord notifications system wide.')
                                    ->label('Notifications'),
                                Select::make('discord_settings.discord_channel')
                                    ->visible(fn (Get $get): bool => filled($this->discordServerName()) && $get('discord_settings.discord_enabled'))
                                    ->label('Channel')
                                    ->searchable()
                                    ->preload()
                                    ->options(function () {
                                        $guildId = data_get(resolve(IntegrationSettings::class)->discord_settings, 'discord_server');

                                        if (blank($guildId)) {
                                            return [];
                                        }

                                        return collect(DiscordService::getChannels($guildId))
                                            ->filter(fn ($data): bool => (string) data_get($data, 'type') === '0')
                                            ->mapWithKeys(fn ($data): array => [data_get($data, 'id') => data_get($data, 'name')]);
                                    })
                                    ->helperText('Select the channel the notifications will be posted to.'),
                                Toggle::make('discord_settings.sync_roles')
                                    ->label('Sync Rank & Unit Roles')
                                    ->visible(fn (): bool => filled($this->discordServerName()))
                                    ->helperText('Give linked members a Discord role for their rank and unit, and swap it automatically when they are promoted or reassigned. PERSCOM only changes the roles it creates.'),
                                Toggle::make('discord_settings.sync_nicknames')
                                    ->label('Sync Nicknames')
                                    ->visible(fn (): bool => filled($this->discordServerName()))
                                    ->helperText('Set linked members\' server nicknames to their rank and name, such as "SGT Jane Doe". Discord does not allow bots to rename the server owner.'),
                                TextEntry::make('discord_commands')
                                    ->label('Slash Commands')
                                    ->visible(fn (): bool => filled($this->discordServerName()))
                                    ->state('/profile · /roster · /events · /rsvp')
                                    ->fontFamily('mono')
                                    ->helperText('Members who link Discord from their profile can use these commands in your server with their PERSCOM permissions.'),
                            ]),
                        Tab::make('Slack')
                            ->icon('fab-slack')
                            ->visible(fn (): bool => SlackService::isConfigured())
                            ->schema([
                                TextEntry::make('slack_connection')
                                    ->label('Workspace')
                                    ->state(fn (): string => $this->slackWorkspaceName() ?? 'Not connected')
                                    ->badge()
                                    ->color(fn (): string => filled($this->slackWorkspaceName()) ? 'success' : 'gray')
                                    ->icon(fn (): string => filled($this->slackWorkspaceName()) ? 'heroicon-m-check-circle' : 'heroicon-m-minus-circle')
                                    ->helperText('Connect your Slack workspace to post organization-wide notifications, messages and event reminders to a channel.')
                                    ->afterContent([
                                        Action::make('connectSlack')
                                            ->label(fn (): string => filled($this->slackWorkspaceName()) ? 'Reconnect' : 'Connect Workspace')
                                            ->icon('fab-slack')
                                            ->color(fn (): string => filled($this->slackWorkspaceName()) ? 'gray' : 'primary')
                                            ->url(fn (): string => route('app.integrations.install', ['provider' => 'slack'])),
                                        Action::make('disconnectSlack')
                                            ->label('Disconnect')
                                            ->color('danger')
                                            ->link()
                                            ->visible(fn (): bool => filled($this->slackWorkspaceName()))
                                            ->requiresConfirmation()
                                            ->modalHeading('Disconnect Slack')
                                            ->modalDescription('PERSCOM will stop posting to Slack and its access to your workspace will be revoked.')
                                            ->action(function (): void {
                                                DisconnectSlackWorkspace::handle();

                                                $this->fillForm();
                                            }),
                                    ]),
                                Toggle::make('slack_settings.slack_enabled')
                                    ->live()
                                    ->visible(fn (): bool => filled($this->slackWorkspaceName()))
                                    ->helperText('Enable Slack notifications system wide.')
                                    ->label('Notifications'),
                                Select::make('slack_settings.slack_channel')
                                    ->visible(fn (Get $get): bool => filled($this->slackWorkspaceName()) && $get('slack_settings.slack_enabled'))
                                    ->label('Channel')
                                    ->searchable()
                                    ->preload()
                                    ->options(fn (): array => SlackService::channels())
                                    ->helperText('Select the channel the notifications will be posted to. Then choose Slack as a channel on messages, events and record notifications.'),
                            ]),
                        Tab::make('Single Sign-On (SSO)')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('single_sign_on_key')
                                    ->label('SSO Key')
                                    ->disabled()
                                    ->helperText('Use this Single Sign-On Key to sign JWT access tokens and access PERSCOM resources on the fly through the PERSCOM API.')
                                    ->suffixAction(fn (): Action => Action::make('regenerate')
                                        ->icon('heroicon-o-arrow-path')
                                        ->successNotificationTitle('The SSO key has been successfully regenerated.')
                                        ->requiresConfirmation()
                                        ->modalHeading('Regenerate SSO Key')
                                        ->modalDescription('Are you sure you want to regenerate the SSO key? Any previous JWT access tokens signed with the existing SSO key will be invalid.')
                                        ->action(function (Action $action): void {
                                            $settings = resolve(IntegrationSettings::class);
                                            $settings->single_sign_on_key = Str::random(40);
                                            $settings->save();

                                            $action->success();

                                            $this->fillForm();
                                        })),
                            ]),
                        Tab::make('SMS')
                            ->icon('heroicon-o-phone')
                            ->visible(fn (): bool => filled(config('services.twilio.auth_token')) && filled(config('services.twilio.sid')))
                            ->schema([
                                Toggle::make('sms_settings.sms_enabled')
                                    ->live()
                                    ->helperText("Enable SMS notifications system wide. Text messages will be sent to a user's phone number if they have one on file.")
                                    ->label('Enabled'),
                                TextEntry::make('attempts')
                                    ->visible(fn (Get $get): bool => $get('sms_settings.sms_enabled'))
                                    ->label('Daily SMS Limit')
                                    ->helperText('Each account is limited to a daily limit of SMS text messages. To increase your rate, please reach out to support.')
                                    ->getStateUsing(function (): string {
                                        /** @var TwilioService $service */
                                        $service = resolve(TwilioService::class);

                                        $attempts = $service->limiter->attempts('sms') ?? 0;

                                        return $attempts.' / 50';
                                    }),
                            ]),
                    ]),
            ]);
    }

    /**
     * Connection details are written by the install flow, not the form, so keep them when the form
     * is saved.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);

        $this->shouldSyncDiscordMembers = collect(['sync_roles', 'sync_nicknames'])->contains(fn (string $key): bool => filled(data_get($data, 'discord_settings.'.$key))
            && blank(data_get($settings->discord_settings, $key)));

        $data['discord_settings'] = [
            ...(array) data_get($data, 'discord_settings', []),
            ...Arr::only($settings->discord_settings ?? [], ['discord_server', 'discord_server_name', 'managed_roles']),
        ];

        $data['slack_settings'] = [
            ...(array) data_get($data, 'slack_settings', []),
            ...Arr::only($settings->slack_settings ?? [], ['team_id', 'team_name', 'bot_token']),
        ];

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->shouldSyncDiscordMembers) {
            User::query()
                ->whereNotNull('discord_user_id')
                ->pluck('id')
                ->each(fn (int $id): PendingDispatch => dispatch(new SyncDiscordMember($id)));
        }
    }

    protected function slackWorkspaceName(): ?string
    {
        $settings = resolve(IntegrationSettings::class)->slack_settings ?? [];

        return filled(data_get($settings, 'bot_token')) ? (string) (data_get($settings, 'team_name') ?: 'Slack') : null;
    }

    protected function discordServerName(): ?string
    {
        $settings = resolve(IntegrationSettings::class)->discord_settings ?? [];

        return filled(data_get($settings, 'discord_server'))
            ? (string) (data_get($settings, 'discord_server_name') ?: data_get($settings, 'discord_server'))
            : null;
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
