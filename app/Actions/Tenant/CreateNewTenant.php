<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Models\Domain;
use App\Models\Tenant;

class CreateNewTenant
{
    public function create(string $organization, string $email): Tenant
    {
        $tenant = Tenant::create([
            'name' => $organization,
            'email' => $email,
        ]);

        $tenant->domains()->create([
            'domain' => Domain::generateSubdomain(),
        ]);

        return $tenant;
    }
}
