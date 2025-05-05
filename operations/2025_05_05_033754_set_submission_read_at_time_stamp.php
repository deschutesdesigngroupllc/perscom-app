<?php

declare(strict_types=1);

use App\Models\Submission;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (): void {
            Submission::query()->whereNull('read_at')->update([
                'read_at' => now(),
            ]);
        });
    }
};
