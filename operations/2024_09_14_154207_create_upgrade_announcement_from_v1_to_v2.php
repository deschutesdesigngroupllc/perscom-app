<?php

declare(strict_types=1);

use App\Models\Announcement;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant): void {
            Announcement::create([
                'global' => true,
                'color' => 'info',
                'title' => 'Welcome to PERSCOM, Version 2',
                'content' => 'Please look around and check out the upgrade notes in our docs if you have questions.',
                'expires_at' => now()->addWeeks(2),
            ]);
        });
    }
};
