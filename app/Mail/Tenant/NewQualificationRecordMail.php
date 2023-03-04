<?php

namespace App\Mail\Tenant;

use App\Models\QualificationRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewQualificationRecordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected QualificationRecord $qualificationRecord,
        protected string $url
    ) {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.tenant.new-qualification-record')->subject('New Qualification Record')->with([
            'qualification' => $this->qualificationRecord->qualification?->name,
            'text' => $this->qualificationRecord->text,
            'url' => $this->url,
            'date' => Carbon::parse($this->qualificationRecord->created_at)->toFormattedDateString(),
        ]);
    }
}
