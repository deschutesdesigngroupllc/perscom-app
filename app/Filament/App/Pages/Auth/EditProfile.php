<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Features\AdvancedNotificationsFeature;
use App\Services\UserSettingsService;
use DateTimeZone;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\HtmlString;
use Laravel\Pennant\Feature;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tabs\Tab::make('Profile')
                            ->icon('heroicon-o-user')
                            ->schema([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                Select::make('timezone')
                                    ->preload()
                                    ->searchable()
                                    ->default('UTC')
                                    ->options(collect(DateTimeZone::listIdentifiers())->mapWithKeys(fn ($value, $key) => [$value => $value]))
                                    ->required(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ]),
                        Tabs\Tab::make('SMS')
                            ->visible(fn () => Feature::active(AdvancedNotificationsFeature::class))
                            ->icon('heroicon-o-device-phone-mobile')
                            ->schema([
                                TextInput::make('phone_number')
                                    ->visible(fn () => Feature::active(AdvancedNotificationsFeature::class))
                                    ->nullable()
                                    ->tel()
                                    ->helperText('By providing your phone number, you consent to allow Deschutes Design Group LLC/PERSCOM to send you account-related text messages. Alert and data rates may apply. Remove your phone number to disable SMS text messages.'),
                            ]),
                        Tabs\Tab::make('Social')
                            ->visible(fn () => Feature::active(AdvancedNotificationsFeature::class))
                            ->icon('heroicon-o-globe-americas')
                            ->schema([
                                TextInput::make('discord_user_id')
                                    ->suffixAction(fn (): Action => Action::make('discord')
                                        ->url('https://support.discord.com/hc/en-us/articles/206346498-Where-can-I-find-my-User-Server-Message-ID')
                                        ->openUrlInNewTab()
                                        ->icon('heroicon-o-question-mark-circle')
                                        ->color('gray')
                                    )
                                    ->numeric()
                                    ->label('Discord User ID')
                                    ->helperText(new HtmlString("Your Discord User ID. This should be your Discord <a class='underline' href='https://discord.com/developers/docs/reference#snowflakes' target='_blank'>snowflake</a> ID - not your username or display name.")),
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
