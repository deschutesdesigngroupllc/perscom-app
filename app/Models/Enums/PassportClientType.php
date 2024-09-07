<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum PassportClientType: string implements HasLabel
{
    case AUTHORIZATION_CODE = 'authorization_code';
    case IMPLICIT = 'implicit';
    case CLIENT_CREDENTIALS = 'client_credentials';
    case PASSWORD = 'password';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AUTHORIZATION_CODE => 'Regular Web Application',
            self::IMPLICIT => 'Single Page Web Applications or Native Applications',
            self::CLIENT_CREDENTIALS => 'Machine-to-Machine',
            self::PASSWORD => 'Resource Owner',
        };
    }
}
