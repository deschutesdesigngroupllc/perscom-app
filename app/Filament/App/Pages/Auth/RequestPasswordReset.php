<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Filament\Forms\Components\Turnstile;
use Filament\Schemas\Schema;

class RequestPasswordReset extends \Filament\Auth\Pages\PasswordReset\RequestPasswordReset
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                Turnstile::make('turnstile'),
            ]);
    }
}
