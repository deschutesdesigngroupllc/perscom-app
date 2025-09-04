<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Services\UserSettingsService;
use DateTimeZone;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected ?string $subheading = 'Manage your online profile.';

    /**
     * @throws ImplementationException
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Profile')
                            ->icon('heroicon-o-user')
                            ->schema([
                                $this->getNameFormComponent(),
                                Select::make('timezone')
                                    ->preload()
                                    ->searchable()
                                    ->default('UTC')
                                    ->options(collect(DateTimeZone::listIdentifiers())->mapWithKeys(fn ($value, $key): array => [$value => $value]))
                                    ->required(),
                                /** @phpstan-ignore method.notFound */
                                $this->getEmailFormComponent()
                                    ->label('Email'),
                                TextInput::make('phone_number')
                                    ->label('Phone Number')
                                    ->nullable()
                                    ->tel()
                                    ->helperText('By providing your phone number, you consent to allow Deschutes Design Group LLC/PERSCOM to send you account-related text messages. Alert and data rates may apply. Remove your phone number to disable SMS text messages.'),
                            ]),
                        Tab::make('Social')
                            ->columnSpanFull()
                            ->icon('heroicon-o-device-phone-mobile')
                            ->schema([
                                Section::make('Discord')
                                    ->icon('fab-discord')
                                    ->description(fn (): string => match (false) {
                                        Auth::user()->discord_connected => 'Your Discord account is not currently connected.',
                                        default => 'Your Discord account has been successfully connected.'
                                    })
                                    ->headerActions([
                                        Action::make('discord_connect')
                                            ->hidden(fn (): bool => Auth::user()->discord_connected)
                                            ->icon('fab-discord')
                                            ->color(Color::generateV3Palette('#5865F2'))
                                            ->url(route(FilamentSocialitePlugin::current()->getRoute(), ['provider' => 'discord']))
                                            ->label('Connect Discord Account')
                                            ->button(),
                                        Action::make('discord_disconnect')
                                            ->requiresConfirmation()
                                            ->modalDescription('Are you sure you would like to disconnect your Discord account?')
                                            ->modalSubmitActionLabel('Disconnect')
                                            ->successNotificationTitle('Your Discord account has been successfully disconnected.')
                                            ->visible(fn (): bool => Auth::user()->discord_connected)
                                            ->icon('fab-discord')
                                            ->color('danger')
                                            ->action(function (Action $action): void {
                                                Auth::user()->disconnectDiscordAccount();

                                                $action->success();
                                            })
                                            ->label('Disconnect Discord Account')
                                            ->button(),
                                    ])
                                    ->schema([]),
                                Section::make('Github')
                                    ->icon('fab-github')
                                    ->description(fn (): string => match (false) {
                                        Auth::user()->github_connected => 'Your Github account is not currently connected.',
                                        default => 'Your Github account has been successfully connected.'
                                    })
                                    ->headerActions([
                                        Action::make('github_connect')
                                            ->hidden(fn (): bool => Auth::user()->github_connected)
                                            ->icon('fab-github')
                                            ->color(Color::generateV3Palette('#24292e'))
                                            ->url(route(FilamentSocialitePlugin::current()->getRoute(), ['provider' => 'github']))
                                            ->label('Connect Github Account')
                                            ->button(),
                                        Action::make('github_disconnect')
                                            ->requiresConfirmation()
                                            ->modalDescription('Are you sure you would like to disconnect your Github account?')
                                            ->modalSubmitActionLabel('Disconnect')
                                            ->successNotificationTitle('Your Github account has been successfully disconnected.')
                                            ->visible(fn (): bool => Auth::user()->github_connected)
                                            ->icon('fab-github')
                                            ->color('danger')
                                            ->action(function (Action $action): void {
                                                Auth::user()->disconnectGithubAccount();

                                                $action->success();
                                            })
                                            ->label('Disconnect Github Account')
                                            ->button(),
                                    ])
                                    ->schema([]),
                                Section::make('Google')
                                    ->icon('fab-google')
                                    ->description(fn (): string => match (false) {
                                        Auth::user()->google_connected => 'Your Google account is not currently connected.',
                                        default => 'Your Google account has been successfully connected.'
                                    })
                                    ->headerActions([
                                        Action::make('google_connect')
                                            ->hidden(fn (): bool => Auth::user()->google_connected)
                                            ->icon('fab-google')
                                            ->color(Color::Neutral)
                                            ->url(route(FilamentSocialitePlugin::current()->getRoute(), ['provider' => 'google']))
                                            ->label('Connect Google Account')
                                            ->button(),
                                        Action::make('google_disconnect')
                                            ->requiresConfirmation()
                                            ->modalDescription('Are you sure you would like to disconnect your Google account?')
                                            ->modalSubmitActionLabel('Disconnect')
                                            ->successNotificationTitle('Your Google account has been successfully disconnected.')
                                            ->visible(fn (): bool => Auth::user()->google_connected)
                                            ->icon('fab-google')
                                            ->color('danger')
                                            ->action(function (Action $action): void {
                                                Auth::user()->disconnectGoogleAccount();

                                                $action->success();
                                            })
                                            ->label('Disconnect Google Account')
                                            ->button(),
                                    ])
                                    ->schema([]),
                            ]),
                        Tab::make('Password')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                /** @phpstan-ignore method.notFound */
                                $this->getPasswordFormComponent()
                                    ->helperText('Set a new password for your account.')
                                    ->label('New Password'),
                                /** @phpstan-ignore method.notFound */
                                $this->getPasswordConfirmationFormComponent()
                                    ->label('Confirm New Password'),
                            ]),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return parent::mutateFormDataBeforeFill(array_merge($data, [
            'notifications' => UserSettingsService::get('notifications'),
            'timezone' => UserSettingsService::get('timezone'),
        ]));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        UserSettingsService::save('notifications', data_get($data, 'notifications'));
        UserSettingsService::save('timezone', data_get($data, 'timezone'));

        data_forget($data, ['notifications', 'timezone']);

        return parent::mutateFormDataBeforeFill($data);
    }
}
