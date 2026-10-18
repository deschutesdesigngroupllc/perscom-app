<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Rank;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateRank
{
    /**
     * Find a rank by name (case-insensitive) or create it, optionally attaching a category by name.
     *
     * @param  array{name: string, abbreviation?: string|null, paygrade?: string|null, description?: string|null, category?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Rank
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['nullable', 'string', 'max:255'],
            'paygrade' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($validated): Rank {
            $rank = Rank::query()
                ->withoutGlobalScopes()
                ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
                ->first() ?? Rank::create(Arr::except($validated, 'category'));

            if (filled($validated['category'] ?? null)) {
                AttachCategory::handle($rank, $validated['category']);
            }

            return $rank;
        });
    }
}
