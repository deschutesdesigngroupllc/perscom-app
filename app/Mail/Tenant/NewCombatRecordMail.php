<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\CombatRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewCombatRecordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected CombatRecord $combatRecord, protected string $url)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.new-combat-record')
            ->subject('New Combat Record')
            ->with([
                'text' => $this->combatRecord->text,
                'url' => $this->url,
                'date' => Carbon::parse($this->combatRecord->created_at)->toFormattedDateString(),
            ]);
    }
}
