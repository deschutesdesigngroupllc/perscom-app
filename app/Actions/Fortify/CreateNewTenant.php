<?php

namespace App\Actions\Fortify;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
		Validator::make($input, [
			'organization' => ['required', 'string', 'max:255', Rule::unique(Tenant::class, 'name')],
			'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Tenant::class, 'email')],
			'website' => ['nullable', 'string', 'max:255'],
			'domain' => ['required', 'string', 'max:255'],
		])->validate();

		$tenant = Tenant::create([
			'name' => $input['organization'],
			'email' => $input['email'],
			'website' => $input['website'] ?? null,
		]);

		$tenant->domains()->create([
			'domain' => "{$input['domain']}.perscom.io",
		]);

		return $tenant;
	}
}
