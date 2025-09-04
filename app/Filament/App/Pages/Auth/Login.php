<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App;
use App\Filament\Forms\Components\Turnstile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
            ->components([
                $email->placeholder(fn (): ?string => $demo ? 'demo@perscom.io' : null),
                $password->placeholder(fn (): ?string => $demo ? 'password' : null),
                Turnstile::make('turnstile'),
                $this->getRememberFormComponent(),
            ]);
    }
}
