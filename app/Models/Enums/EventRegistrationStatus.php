<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum EventRegistrationStatus: string implements HasColor, HasLabel
{
    case GOING = 'going';
    case NOT_GOING = 'not_going';
    case INTERESTED = 'interested';
    case WAITLISTED = 'waitlisted';
    case CANCELLED = 'cancelled';
    case DECLINED = 'declined';
    case TENTATIVE = 'tentative';
    case INVITED = 'invited';
    case UNKNOWN = 'unknown';
    case ATTENDED = 'attended';
    case NO_SHOW = 'no_show';

    /**
     * The simplified set of responses offered to members when they RSVP.
     *
     * @return array<int, EventRegistrationStatus>
     */
    public static function memberResponses(): array
    {
        return [
            EventRegistrationStatus::GOING,
            EventRegistrationStatus::TENTATIVE,
            EventRegistrationStatus::NOT_GOING,
        ];
    }

    public function isMemberResponse(): bool
    {
        return in_array($this, EventRegistrationStatus::memberResponses(), true);
    }

    /**
     * The short label shown on member-facing RSVP controls.
     */
    public function getMemberLabel(): string
    {
        return match ($this) {
            EventRegistrationStatus::GOING => 'Going',
            EventRegistrationStatus::TENTATIVE => 'Maybe',
            EventRegistrationStatus::NOT_GOING => "Can't Go",
            default => $this->getLabel(),
        };
    }

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
            EventRegistrationStatus::GOING, EventRegistrationStatus::ATTENDED => 'success',
            EventRegistrationStatus::NOT_GOING, EventRegistrationStatus::NO_SHOW, EventRegistrationStatus::CANCELLED, EventRegistrationStatus::DECLINED => 'danger',
            EventRegistrationStatus::INTERESTED => 'warning',
            EventRegistrationStatus::WAITLISTED, EventRegistrationStatus::TENTATIVE, EventRegistrationStatus::INVITED, EventRegistrationStatus::UNKNOWN => 'info',
        };
    }
}
