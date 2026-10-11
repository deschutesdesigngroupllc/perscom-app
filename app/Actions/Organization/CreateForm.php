<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use App\Models\Field;
use App\Models\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateForm
{
    /**
     * Find a form by name (case-insensitive) or create it, then make sure each of its fields exists on the form.
     *
     * @param  array{
     *     name: string,
     *     description?: string|null,
     *     instructions?: string|null,
     *     success_message?: string|null,
     *     is_public?: bool,
     *     fields?: array<int, array{name: string, type?: string, required?: bool, placeholder?: string|null, help?: string|null, options?: array<int, string>}>
     * }  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Form
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'success_message' => ['nullable', 'string'],
            'is_public' => ['nullable', 'boolean'],
            'fields' => ['nullable', 'array'],
            'fields.*.name' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['nullable', Rule::enum(FieldType::class)],
            'fields.*.required' => ['nullable', 'boolean'],
            'fields.*.placeholder' => ['nullable', 'string'],
            'fields.*.help' => ['nullable', 'string'],
            'fields.*.options' => ['nullable', 'array'],
            'fields.*.options.*' => ['string'],
        ])->validate();

        return DB::transaction(function () use ($validated): Form {
            $form = Form::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
                ->first() ?? Form::create([
                    ...Arr::except($validated, 'fields'),
                    'is_public' => $validated['is_public'] ?? false,
                    'slug' => self::uniqueSlug($validated['name']),
                ]);

            collect($validated['fields'] ?? [])
                ->values()
                ->each(function (array $field, int $index) use ($form): void {
                    $key = Str::slug($field['name'], '_');

                    if ($form->fields()->where('key', $key)->exists()) {
                        return;
                    }

                    $type = FieldType::tryFrom($field['type'] ?? '') ?? FieldType::FIELD_TEXT;
                    $options = $field['options'] ?? [];

                    $model = Field::create([
                        'name' => $field['name'],
                        'key' => $key,
                        'type' => $type,
                        'cast' => $type->getCast(),
                        'required' => $field['required'] ?? false,
                        'placeholder' => $field['placeholder'] ?? null,
                        'help' => $field['help'] ?? null,
                        'options_type' => filled($options) ? FieldOptionsType::ARRAY : null,
                        'options' => filled($options) ? json_encode(array_values($options)) : null,
                    ]);

                    $form->fields()->attach($model->getKey(), ['order' => $index + 1]);
                });

            return $form;
        });
    }

    protected static function uniqueSlug(string $name): string
    {
        $slug = $base = Str::slug($name);
        $suffix = 2;

        while (Form::query()->where('slug', $slug)->exists()) {
            $slug = sprintf('%s-%d', $base, $suffix++);
        }

        return $slug;
    }
}
