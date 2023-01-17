<?php

namespace App\Actions\Fortify;

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
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
            'organization' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Tenant::class, 'name'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Tenant::class, 'email'),
            ]
        ])->validate();

        $tenant = Tenant::withoutEvents(function () use ($input) {
            return Tenant::create([
                'name' => $input['organization'],
                'email' => $input['email']
            ]);
        });

        $domain = Domain::withoutEvents(function () use ($tenant, $input) {
            return $tenant->domains()->create([
                'domain' => Domain::generateSubdomain(),
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
