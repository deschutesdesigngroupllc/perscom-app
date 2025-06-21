<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth;

use App;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                App\Filament\Forms\Components\Turnstile::make('turnstile'),
                $this->getRememberFormComponent(),
            ]);
    }
}
