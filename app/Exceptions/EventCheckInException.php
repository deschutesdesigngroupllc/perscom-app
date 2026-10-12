<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class EventCheckInException extends Exception
{
    public static function outsideWindow(): self
    {
        return new self('Check-in is only available from one hour before the event starts until one hour after it ends.');
    }

    public static function cancelled(): self
    {
        return new self('This occurrence of the event has been cancelled.');
    }

    public static function registrationDisabled(): self
    {
        return new self('Registration is disabled for this event, so you cannot check in without being registered.');
    }
}
