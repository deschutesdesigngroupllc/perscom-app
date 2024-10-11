<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Field;
use App\Services\FieldService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class FieldDataRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $values = Arr::wrap($value);

        $fields = Field::query()->whereIn('key', array_keys($values))->get();

        $rules = FieldService::getValidationRules($fields);

        if ($rules->isNotEmpty()) {
            $validator = Validator::make($values, $rules->toArray());

            if ($validator->fails()) {
                $fail($validator->getMessageBag()->first());
            }
        }
    }
}
