<?php

namespace App\Actions\Fortify;

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Events\DomainCreated;
use Stancl\Tenancy\Events\TenantCreated;

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
        Validator::make($input, [
            'organization' => ['required', 'string', 'max:255', Rule::unique(Tenant::class, 'name')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Tenant::class, 'email')],
            'website' => ['nullable', 'string', 'max:255'],
            'domain' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'lowercase',
                Rule::unique(Domain::class, 'domain'),
            ],
        ])->validate();

        $tenant = Tenant::withoutEvents(function () use ($input) {
            return Tenant::create([
                'name' => $input['organization'],
                'email' => $input['email'],
                'website' => $input['website'] ?? null,
            ]);
        });

        $domain = Domain::withoutEvents(function () use ($tenant, $input) {
            return $tenant->domains()->create([
                'domain' => Str::lower($input['domain']),
            ]);
        });

        $tenant->load('domains');

        Event::dispatch(new TenantCreated($tenant));
        event('eloquent.created: '.Tenant::class, $tenant);
        Event::dispatch(new DomainCreated($domain));
        event('eloquent.created: '.Domain::class, $domain);

        return $tenant;
    }
}
