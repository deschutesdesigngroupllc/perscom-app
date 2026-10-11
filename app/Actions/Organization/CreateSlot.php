<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Position;
use App\Models\Slot;
use App\Models\Specialty;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateSlot
{
    /**
     * Find a slot by name, position, and specialty or create it, and attach it to the unit when given.
     *
     * @param  array{name: string, description?: string|null, position_id?: int|null, specialty_id?: int|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input, ?Unit $unit = null): Slot
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'position_id' => ['nullable', 'integer', Rule::exists(Position::class, 'id')],
            'specialty_id' => ['nullable', 'integer', Rule::exists(Specialty::class, 'id')],
        ])->validate();

        return DB::transaction(function () use ($validated, $unit): Slot {
            $slot = Slot::query()
                ->withoutGlobalScopes()
                ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
                ->where('position_id', $validated['position_id'] ?? null)
                ->where('specialty_id', $validated['specialty_id'] ?? null)
                ->first() ?? Slot::create($validated);

            if ($unit instanceof Unit && ! $unit->slots()->whereKey($slot->getKey())->exists()) {
                $unit->slots()->attach($slot->getKey());
            }

            return $slot;
        });
    }
}
