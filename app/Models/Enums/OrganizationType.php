<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrganizationType: string implements HasLabel
{
    case MILITARY = 'military';
    case FIRE_SERVICE = 'fire-service';
    case LAW_ENFORCEMENT = 'law-enforcement';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            OrganizationType::MILITARY => 'Military or milsim unit',
            OrganizationType::FIRE_SERVICE => 'Fire or EMS department',
            OrganizationType::LAW_ENFORCEMENT => 'Law enforcement agency',
            OrganizationType::OTHER => 'Something else',
        };
    }

    /**
     * The blueprint preset to apply during setup, if one ships for this type.
     */
    public function blueprintPreset(): ?string
    {
        return $this === OrganizationType::OTHER ? null : $this->value;
    }
}
