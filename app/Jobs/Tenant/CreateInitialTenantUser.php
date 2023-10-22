<?php

namespace App\Jobs\Tenant;

use App\Mail\Tenant\NewTenantMail;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateInitialTenantUser implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Tenant $tenant, protected bool $sendMail = true)
    {
        //
    }

    public function handle(): void
    {
        $password = Str::password();
        $user = $this->tenant->run(function () use ($password) {
            $createsNewUser = app()->make(CreatesNewUsers::class);
            $user = $createsNewUser->create([
                'name' => 'Admin',
                'email' => $this->tenant->email,
                'password' => $password,
                'password_confirmation' => $password,
            ]);
            $user->assignRole('Admin');

            return $user;
        });

        if ($this->sendMail) {
            Mail::to($user)->send(new NewTenantMail($this->tenant, $user, $password));
        }
    }

    public function failed($exception): void
    {
        Log::error('Failed to create initial tenant user', [
            'exception' => $exception,
        ]);
    }
}
