<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Unit;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnitParentRule implements ValidationRule
{
    public function __construct(protected ?Unit $unit = null)
    {
        //
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value) || ! $this->unit instanceof Unit || ! $this->unit->exists) {
            return;
        }

        $parentId = (int) $value;

        if ($parentId === (int) $this->unit->getKey()) {
            $fail('A unit cannot be its own parent.');

            return;
        }

        if (in_array($parentId, $this->unit->descendantIds(), true)) {
            $fail('A unit cannot be nested beneath one of its own sub-units.');
        }
    }
}
