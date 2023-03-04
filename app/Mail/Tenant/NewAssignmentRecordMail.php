<?php

namespace App\Mail\Tenant;

use App\Models\AssignmentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewAssignmentRecordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected AssignmentRecord $assignmentRecord,
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
        return $this->markdown('emails.tenant.new-assignment-record')->subject('New Assignment Record')->with([
            'unit' => $this->assignmentRecord->unit?->name,
            'position' => $this->assignmentRecord->position?->name,
            'specialty' => $this->assignmentRecord->specialty?->name,
            'text' => $this->assignmentRecord->text,
            'url' => $this->url,
            'date' => Carbon::parse($this->assignmentRecord->created_at)->toFormattedDateString(),
        ]);
    }
}
