<?php

namespace App\Mail\User;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserLoginInformationMail extends Mailable implements ShouldQueue
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
        return $this->markdown('emails.user.new-login')->subject('Your New Account Information')->with([
            'url' => $this->url,
            'email' => $this->email,
            'password' => $this->password,
        ]);
    }
}
