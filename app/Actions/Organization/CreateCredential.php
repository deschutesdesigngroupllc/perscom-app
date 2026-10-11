<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Credential;
use App\Models\Enums\CredentialType;
use App\Models\Issuer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateCredential
{
    /**
     * Find a credential by name (case-insensitive) or create it, creating its issuer by name when given.
     *
     * @param  array{name: string, description?: string|null, type?: string|null, issuer?: string|null}  $input
     *
     * @throws ValidationException
     */
    public static function handle(array $input): Credential
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', Rule::enum(CredentialType::class)],
            'issuer' => ['nullable', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($validated): Credential {
            $existing = Credential::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($validated['name'])])
                ->first();

            if ($existing instanceof Credential) {
                return $existing;
            }

            $issuer = filled($validated['issuer'] ?? null)
                ? Issuer::query()->whereRaw('LOWER(name) = ?', [Str::lower($validated['issuer'])])->first() ?? Issuer::create(['name' => $validated['issuer']])
                : null;

            return Credential::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'] ?? CredentialType::CERTIFICATION->value,
                'issuer_id' => $issuer?->getKey(),
            ]);
        });
    }
}
