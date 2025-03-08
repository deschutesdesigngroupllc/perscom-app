<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant): void {
            activity('newsfeed')
                ->withProperties([
                    'text' => "Welcome to our platform! We're thrilled to have you on board as a user. Get ready to experience a powerful and intuitive solution that will streamline your personnel management and revolutionize how you organize and track your team. For more information and tutorials, please visit our documentation available at <a href='https://docs.perscom.io' target='_blank'>https://docs.perscom.io</a>.",
                    'headline' => "Welcome to PERSCOM Personnel Management System {$tenant->name}",
                ])
                ->event('created')
                ->causedBy(User::where('email', $tenant->email)->first())
                ->log('created');
        });
    }
};
