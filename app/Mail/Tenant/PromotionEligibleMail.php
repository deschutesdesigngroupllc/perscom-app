<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PromotionEligibleMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $title,
        public string $rank,
    ) {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.promotion-eligible')
            ->subject($this->title)
            ->with([
                'rank' => $this->rank,
            ]);
    }
}
