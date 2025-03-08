<?php

declare(strict_types=1);

use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
            $tenant->updateQuietly([
                'setup_completed_at' => $tenant->created_at,
            ]);
        });
    }
};
