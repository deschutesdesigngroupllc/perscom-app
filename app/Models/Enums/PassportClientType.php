<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum PassportClientType: string implements HasDescription, HasLabel
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

    public function getDescription(): ?string
    {
        return match ($this) {
            self::AUTHORIZATION_CODE => 'The authorization code grant is used for server-side applications where a user is redirected to an authorization server to grant access, and an authorization code is exchanged for an access token, ensuring secure token handling.',
            self::IMPLICIT => 'The implicit grant is used for client-side applications, like single-page apps, where access tokens are directly returned to the client without an intermediate authorization code, simplifying the flow for user authentication.',
            self::CLIENT_CREDENTIALS => 'The client credentials grant is used when an application needs to authenticate itself to access its own resources or perform actions on behalf of itself, without needing user involvement or consent.',
            self::PASSWORD => 'The password credentials grant is used when a user directly provides their username and password to a client application, allowing the application to request an access token on behalf of the user, typically in trusted environments.',
        };
    }
}
