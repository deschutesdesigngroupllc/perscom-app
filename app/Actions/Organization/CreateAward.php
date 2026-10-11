<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Award;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateAward
{
    /**
     * Find an award by name (case-insensitive) or create it, optionally attaching a category by name.
     *
     * @param  array{name: string, description?: string|null, category?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Award
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($validated): Award {
            $award = Award::query()
                ->withoutGlobalScopes()
                ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
                ->first() ?? Award::create(Arr::except($validated, 'category'));

            if (filled($validated['category'] ?? null)) {
                AttachCategory::handle($award, $validated['category']);
            }

            return $award;
        });
    }
}
