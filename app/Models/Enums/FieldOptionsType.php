<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum FieldOptionsType: string implements HasDescription, HasLabel
{
    case ARRAY = 'array';
    case MODEL = 'model';

    public function getLabel(): string
    {
        return match ($this) {
            FieldOptionsType::ARRAY => 'Array',
            FieldOptionsType::MODEL => 'Resource',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            FieldOptionsType::ARRAY => 'Provide a pre-defined list of options to select from.',
            FieldOptionsType::MODEL => 'Select from a list of resources such as awards, ranks, or qualifications.',
        };
    }
}
