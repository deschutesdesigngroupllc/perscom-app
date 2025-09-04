<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Filament\Forms\Components\Turnstile;
use Filament\Schemas\Schema;

class Register extends \Filament\Auth\Pages\Register
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                Turnstile::make('turnstile'),
            ]);
    }
}
