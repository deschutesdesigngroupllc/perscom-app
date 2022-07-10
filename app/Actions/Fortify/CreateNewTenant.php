<?php

namespace App\Actions\Fortify;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewTenant
{
    /**
     * Validate and create a newly registered tenant.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        if (isset($input['domain'])) {
            $domain = app()->environment() === 'production' ? '.perscom.io' : '.localhost';
            if (!Str::contains($input['domain'], $domain)) {
                $input['domain'] = "{$input['domain']}$domain";
            }
        }

        Validator::make($input, [
            'organization' => ['required', 'string', 'max:255', Rule::unique(Tenant::class, 'name')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Tenant::class, 'email')],
            'website' => ['nullable', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', Rule::unique(Domain::class, 'domain')],
        ])->validate();

        $tenant = Tenant::create([
            'name' => $input['organization'],
            'email' => $input['email'],
            'website' => $input['website'] ?? null,
        ]);

        $tenant->domains()->create([
            'domain' => $input['domain'],
        ]);

        return $tenant;
    }
}
