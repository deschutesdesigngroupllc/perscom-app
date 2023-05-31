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
    use Queueable;
    use SerializesModels;

    /**
     * @var mixed
     */
    protected $url;

    /**
     * @var mixed
     */
    protected $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant, User $user, protected string $password)
    {
        $this->url = $tenant->url;
        $this->email = $user->email;
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
