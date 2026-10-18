<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Specialty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateSpecialty
{
    /**
     * Find a specialty by name (case-insensitive) or create it.
     *
     * @param  array{name: string, abbreviation?: string|null, description?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Specialty
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ])->validate();

        return DB::transaction(fn (): Specialty => Specialty::query()
            ->withoutGlobalScopes()
            ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
            ->first() ?? Specialty::create($validated));
    }
}
