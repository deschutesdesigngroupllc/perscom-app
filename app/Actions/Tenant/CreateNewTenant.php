<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Models\Domain;
use App\Models\Enums\OrganizationType;
use App\Models\Tenant;

class CreateNewTenant
{
    /**
     * The founder's name, requested subdomain and organization type are kept on the tenant so the setup
     * job, which runs once the database exists, can name the founder, claim the subdomain and apply the
     * matching structure.
     */
    public static function handle(string $organization, string $email, ?string $founderName = null, ?string $requestedSubdomain = null, ?OrganizationType $organizationType = null): Tenant
    {
        $tenant = Tenant::create(array_filter([
            'name' => $organization,
            'email' => $email,
            'founder_name' => $founderName,
            'requested_subdomain' => $requestedSubdomain,
            'organization_type' => $organizationType?->value,
        ], filled(...)));

        $tenant->domains()->create([
            'domain' => Domain::generateSubdomain(),
        ]);

        return $tenant;
    }
}
