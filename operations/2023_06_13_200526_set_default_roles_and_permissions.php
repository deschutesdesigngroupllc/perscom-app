<?php

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
            nova_set_setting_value('default_permissions', []);
            nova_set_setting_value('default_roles', ['User']);
        });
    }
};
