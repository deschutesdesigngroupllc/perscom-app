<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class EventRegistrationException extends Exception
{
    public static function closed(): self
    {
        return new self('Registration for this event is closed.');
    }

    public static function cancelled(): self
    {
        return new self('This occurrence of the event has been cancelled.');
    }

    public static function ended(): self
    {
        return new self('This event has already ended.');
    }

    public static function invalidResponse(): self
    {
        return new self('That is not a valid response for this event.');
    }
}
