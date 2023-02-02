<?php

namespace App\Mail\Tenant;

use App\Models\CombatRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewCombatRecordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected CombatRecord $combatRecord, protected string $url)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.tenant.new-combat-record')->subject('New Combat Record')->with([
            'text' => $this->combatRecord->text,
            'url' => $this->url,
            'date' => Carbon::parse($this->combatRecord->created_at)->toFormattedDateString(),
        ]);
    }
}
