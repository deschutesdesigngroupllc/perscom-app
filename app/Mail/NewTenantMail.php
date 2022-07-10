<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTenantMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Tenant
     */
    protected $tenant;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var
     */
    protected $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant, User $user, $password)
    {
        $this->tenant = $tenant;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.tenant.new')
            ->subject('Your Organization Is Now Ready')
            ->with([
                'url' => $this->tenant->url,
                'email' => $this->user->email,
                'password' => $this->password,
            ]);
    }
}
