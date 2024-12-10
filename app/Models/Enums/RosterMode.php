<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum RosterMode: string implements HasDescription, HasLabel
{
    case AUTOMATIC = 'automatic';
    case MANUAL = 'manual';

    public function getLabel(): ?string
    {
        return Str::title($this->value);
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            RosterMode::AUTOMATIC => 'The roster must be manually built by configuring groups, units and assigning available personnel slots to specific roster locations. ',
            RosterMode::MANUAL => 'The roster will be built automatically based on the groups and units that are created. Personnel will be sorted according to the roster sort order.'
        };
    }
}
