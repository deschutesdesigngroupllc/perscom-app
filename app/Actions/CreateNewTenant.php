<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Domain;
use App\Models\Tenant;
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
            'privacy.required' => 'You must agree to the policies.',
        ])->validate();

        $tenant = Tenant::create([
            'name' => $input['organization'],
            'email' => $input['email'],
        ]);

        $tenant->domains()->create([
            'domain' => Domain::generateSubdomain(),
        ]);

        return $tenant;
    }
}
