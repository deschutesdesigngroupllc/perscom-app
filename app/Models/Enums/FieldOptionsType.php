<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum FieldOptionsType: string implements HasDescription, HasLabel
{
    case Array = 'array';
    case Model = 'model';

    public function getLabel(): string
    {
        return match ($this) {
            FieldOptionsType::Array => 'Array',
            FieldOptionsType::Model => 'Resource',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            FieldOptionsType::Array => 'Provide a pre-defined list of options to select from.',
            FieldOptionsType::Model => 'Select from a list of resources such as awards, ranks, or qualifications.',
        };
    }
}
