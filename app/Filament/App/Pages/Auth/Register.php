<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Filament\Forms\Components\Turnstile;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                Turnstile::make('turnstile'),
            ]);
    }
}
