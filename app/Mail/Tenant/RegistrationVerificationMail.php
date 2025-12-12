<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class RegistrationVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected string $verificationUrl;

    public function __construct(Registration $registration)
    {
        $this->verificationUrl = URL::signedRoute('web.register.verify', ['registration' => $registration], now()->addDay());
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.verification')
            ->subject('Please verify your organization registration')
            ->with([
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}
