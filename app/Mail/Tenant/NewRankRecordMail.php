<?php

namespace App\Mail\Tenant;

use App\Models\RankRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewRankRecordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected RankRecord $rankRecord, protected string $url)
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
        return $this->markdown('emails.tenant.new-rank-record')->subject('New Rank Record')->with([
            'rank' => $this->rankRecord->rank?->name,
            'text' => $this->rankRecord->text,
            'url' => $this->url,
            'date' => Carbon::parse($this->rankRecord->created_at)->toFormattedDateString(),
        ]);
    }
}
