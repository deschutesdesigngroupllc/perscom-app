<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateStatus
{
    /**
     * Find a status by name (case-insensitive) or create it.
     *
     * @param  array{name: string, color?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Status
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(fn (): Status => Status::query()
            ->withoutGlobalScopes()
            ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
            ->first() ?? Status::create($validated));
    }
}
