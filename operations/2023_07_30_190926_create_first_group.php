<?php

declare(strict_types=1);

use App\Models\Group;
use App\Models\Tenant;
use App\Models\Unit;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant): void {
            $group = Group::create([
                'name' => 'Group 1',
                'description' => 'This is your first default group.',
            ]);

            Unit::all()->each(function ($unit) use ($group): void {
                $unit->groups()->attach($group);
            });
        });
    }
};
