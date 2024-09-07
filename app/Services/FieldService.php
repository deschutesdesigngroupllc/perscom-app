<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Field;
use Illuminate\Support\Collection;

class FieldService
{
    public static function getValidationRules(mixed $fields): Collection
    {
        return collect($fields)->filter(fn (Field $field) => filled($field->validation_rules))->mapWithKeys(fn (Field $field) => [$field->key => $field->validation_rules]);
    }
}
