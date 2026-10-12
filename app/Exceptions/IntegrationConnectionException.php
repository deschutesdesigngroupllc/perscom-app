<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class IntegrationConnectionException extends Exception
{
    public static function discordServerAlreadyConnected(): self
    {
        return new self('That Discord server is already connected to another PERSCOM organization. Disconnect it there first.');
    }

    public static function authorizationFailed(string $provider): self
    {
        return new self(sprintf('We could not finish connecting %s. Please try again.', $provider));
    }
}
