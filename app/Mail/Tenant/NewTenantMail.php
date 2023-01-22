<?php

namespace App\Mail\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTenantMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var mixed
     */
    protected $url;

    /**
     * @var mixed
     */
    protected $email;

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
        $this->url = $tenant->url;
        $this->email = $user->email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.tenant.new')->subject('Your Organization Is Now Ready')->with([
            'url' => $this->url,
            'email' => $this->email,
            'password' => $this->password,
        ]);
    }
}
