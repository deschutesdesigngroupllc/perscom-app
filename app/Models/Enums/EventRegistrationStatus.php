<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum EventRegistrationStatus: string implements HasColor, HasLabel
{
    case Going = 'going';
    case NotGoing = 'not_going';
    case Interested = 'interested';
    case Waitlisted = 'waitlisted';
    case Cancelled = 'cancelled';
    case Declined = 'declined';
    case Tentative = 'tentative';
    case Invited = 'invited';
    case Unknown = 'unknown';
    case Attended = 'attended';
    case NoShow = 'no_show';

    public function getLabel(): string
    {
        return Str::of($this->value)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    public function getColor(): string
    {
        return match ($this) {
            EventRegistrationStatus::Going, EventRegistrationStatus::Attended => 'success',
            EventRegistrationStatus::NotGoing, EventRegistrationStatus::NoShow, EventRegistrationStatus::Cancelled, EventRegistrationStatus::Declined => 'danger',
            EventRegistrationStatus::Interested => 'warning',
            EventRegistrationStatus::Waitlisted, EventRegistrationStatus::Tentative, EventRegistrationStatus::Invited, EventRegistrationStatus::Unknown => 'info',
        };
    }
}
