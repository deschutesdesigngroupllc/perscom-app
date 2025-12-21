<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant): void {
            try {
                $role = Role::where('name', 'User')->first();
                $role?->revokePermissionTo('create:submission');
            } catch (Exception $exception) {
            }
        });
    }
};
