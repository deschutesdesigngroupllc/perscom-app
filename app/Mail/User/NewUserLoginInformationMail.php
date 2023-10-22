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
    use Queueable;
    use SerializesModels;

    protected string $url;

    protected string $email;

    public function __construct(Tenant $tenant, User $user, protected string $password)
    {
        $this->url = $tenant->url;
        $this->email = $user->email;
    }

    public function build(): static
    {
        return $this->markdown('emails.user.new-login')
            ->subject('Your New Account Information')
            ->with([
                'url' => $this->url,
                'email' => $this->email,
                'password' => $this->password,
            ]);
    }
}
