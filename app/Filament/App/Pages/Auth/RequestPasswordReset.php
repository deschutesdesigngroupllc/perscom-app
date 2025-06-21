<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Filament\Forms\Components\Turnstile;
use Filament\Forms\Form;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                Turnstile::make('turnstile'),
            ]);
    }
}
