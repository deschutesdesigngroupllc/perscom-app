<?php

use App\Models\Role;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    /**
     * Process the operation.
     */
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant) {
            $role = Role::where('name', 'User')->first();
            $role?->revokePermissionTo('create:submission');
        });
    }
};
