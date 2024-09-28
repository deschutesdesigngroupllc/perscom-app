<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Features\SingleSignOnFeature;
use App\Filament\App\Clusters\Settings;
use App\Settings\IntegrationSettings as IntegrationSettingsClass;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class Integration extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Integrations';

    protected static string $settings = IntegrationSettingsClass::class;

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Feature::active(SingleSignOnFeature::class)
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
                        Tabs\Tab::make('Single Sign-On (SSO)')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('single_sign_on_key')
                                    ->label('SSO Key')
                                    ->disabled()
                                    ->helperText('Use this Single Sign-On Key to sign JWT access tokens and access PERSCOM.io resources on the fly through the PERSCOM.io API.')
                                    ->suffixAction(function () {
                                        return Action::make('regenerate')
                                            ->icon('heroicon-o-arrow-path')
                                            ->successNotificationTitle('The SSO key has been successfully regenerated.')
                                            ->action(function (Action $action) {
                                                $settings = app(IntegrationSettingsClass::class);
                                                $settings->single_sign_on_key = Str::random(40);
                                                $settings->save();

                                                $action->success();

                                                $this->fillForm();
                                            });
                                    }),
                            ]),
                    ]),
            ]);
    }
}
