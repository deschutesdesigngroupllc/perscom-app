<?php

namespace App\Actions\Fortify;

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateNewTenant
{
    /**
     * @param  array<string, mixed>  $input
     *
     * @throws ValidationException|Throwable
     */
    public function create(array $input): Tenant
    {
        Validator::make($input, [
            'organization' => ['required', 'string', 'max:255', Rule::unique(Tenant::class, 'name')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Tenant::class, 'email')],
            'privacy' => ['required', 'boolean'],
        ], [
            'privacy.required' => 'You must agree to the Privacy Policy.',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $tenant = Tenant::create([
                'name' => $input['organization'],
                'email' => $input['email'],
            ]);

            return tap($tenant, function ($tenant) {
                $tenant->domains()->create([
                    'domain' => Domain::generateSubdomain(),
                ]);
            })->fresh();
        });
    }
}
