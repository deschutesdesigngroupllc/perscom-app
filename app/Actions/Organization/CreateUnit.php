<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Group;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateUnit
{
    /**
     * Find a unit by name (case-insensitive) or create it, and attach it to the group when given.
     *
     * @param  array{name: string, description?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input, ?Group $group = null): Unit
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ])->validate();

        return DB::transaction(function () use ($validated, $group): Unit {
            $unit = Unit::query()
                ->withoutGlobalScopes()
                ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
                ->first() ?? Unit::create($validated);

            if ($group instanceof Group && ! $unit->groups()->whereKey($group->getKey())->exists()) {
                $unit->groups()->attach($group->getKey());
            }

            return $unit;
        });
    }
}
