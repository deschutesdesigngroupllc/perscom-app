<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Filament\Forms\Components\Turnstile;
use App\Settings\RegistrationSettings;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Schemas\Schema;

class Register extends \Filament\Auth\Pages\Register
{
    public function mount(): void
    {
        abort_unless(RegistrationSettings::isOpen(), 404);

        parent::mount();
    }

    public function register(): ?RegistrationResponse
    {
        abort_unless(RegistrationSettings::isOpen(), 404);

        return parent::register();
    }

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
