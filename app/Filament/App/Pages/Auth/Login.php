<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        $demo = App::isDemo();

        /** @var TextInput $email */
        $email = $this->getEmailFormComponent();

        /** @var TextInput $password */
        $password = $this->getPasswordFormComponent();

        return $form
            ->schema([
                $email->placeholder(fn () => $demo ? 'demo@perscom.io' : null),
                $password->placeholder(fn () => $demo ? 'password' : null),
                $this->getRememberFormComponent(),
            ]);
    }
}
