<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Filament\Forms\Components\Turnstile;
use App\Settings\RegistrationSettings;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\App;

class Login extends \Filament\Auth\Pages\Login
{
    public function form(Schema $schema): Schema
    {
        $demo = App::isDemo();

        /** @var TextInput $email */
        $email = $this->getEmailFormComponent();

        /** @var TextInput $password */
        $password = $this->getPasswordFormComponent();

        return $schema
            ->columns(1)
            ->components([
                $email->placeholder(fn (): ?string => $demo ? 'demo@perscom.io' : null),
                $password->placeholder(fn (): ?string => $demo ? 'password' : null),
                Turnstile::make('turnstile'),
                $this->getRememberFormComponent(),
            ]);
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (blank($this->userUndertakingMultiFactorAuthentication) && ! RegistrationSettings::isOpen()) {
            return null;
        }

        return parent::getSubheading();
    }
}
