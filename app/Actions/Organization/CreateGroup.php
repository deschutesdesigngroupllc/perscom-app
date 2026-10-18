<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateGroup
{
    /**
     * Find a group by name (case-insensitive) or create it.
     *
     * @param  array{name: string, description?: string|null, icon?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Group
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(fn (): Group => Group::query()
            ->withoutGlobalScopes()
            ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
            ->first() ?? Group::create($validated));
    }
}
