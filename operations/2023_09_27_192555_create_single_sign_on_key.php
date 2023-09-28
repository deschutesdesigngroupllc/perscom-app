<?php

use App\Models\Tenant;
use Illuminate\Support\Str;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    /**
     * Process the operation.
     */
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant) {
            nova_set_setting_value('single_sign_on_key', Str::random(40));
        });
    }
};
