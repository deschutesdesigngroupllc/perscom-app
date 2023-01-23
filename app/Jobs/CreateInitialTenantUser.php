<?php

namespace App\Jobs;

use App\Mail\Tenant\NewTenantMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class CreateInitialTenantUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  TenantWithDatabase  $tenant
     * @param  bool  $sendMail
     */
    public function __construct(protected TenantWithDatabase $tenant, protected bool $sendMail = true)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $password = Str::random();
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
}
