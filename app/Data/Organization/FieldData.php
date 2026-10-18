<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class FieldData extends Data
{
    /**
     * @param  string  $type  One of the FieldType values, e.g. text, textarea, select, boolean, date, email, number.
     * @param  array<int, string>  $options  The choices for a select field.
     */
    public function __construct(
        public string $name,
        public string $type = 'text',
        public bool $required = false,
        public ?string $placeholder = null,
        public ?string $help = null,
        public array $options = [],
    ) {}
}
