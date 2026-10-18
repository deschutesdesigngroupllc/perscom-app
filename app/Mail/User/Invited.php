<?php

declare(strict_types=1);

namespace App\Mail\User;

use App\Models\Enums\InviteType;
use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Invited extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected Invite $invite, protected string $url, protected string $organization)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.user.invited')
            ->subject(__('You have been invited to join :organization', ['organization' => $this->organization]))
            ->with([
                'claim' => $this->invite->type === InviteType::CLAIM,
                'expires' => $this->invite->expires_at?->toDayDateTimeString(),
                'inviter' => $this->invite->invitedBy?->name,
                'organization' => $this->organization,
                'url' => $this->url,
            ]);
    }
}
