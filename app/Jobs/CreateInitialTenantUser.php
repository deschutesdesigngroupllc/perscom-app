<?php

namespace App\Jobs;

use App\Mail\NewTenantMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class CreateInitialTenantUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * @var TenantWithDatabase
	 */
	protected $tenant;

	/**
	 * @param  TenantWithDatabase  $tenant
	 */
	public function __construct(TenantWithDatabase $tenant)
	{
		$this->tenant = $tenant;
	}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$password = Str::random('16');
        $user = $this->tenant->run(function () use ($password) {
        	$user = User::create([
        		'name' => 'Admin',
		        'email' => $this->tenant->email,
		        'password' => Hash::make($password)
	        ]);
        	$user->assignRole('Admin');
        	return $user;
        });

        Mail::to($user)->send(new NewTenantMail($this->tenant, $user, $password));
    }
}
