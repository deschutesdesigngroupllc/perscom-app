<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Position;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreatePosition
{
    /**
     * Find a position by name (case-insensitive) or create it.
     *
     * @param  array{name: string, description?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Position
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ])->validate();

        return DB::transaction(fn (): Position => Position::query()
            ->withoutGlobalScopes()
            ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
            ->first() ?? Position::create($validated));
    }
}
