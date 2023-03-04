<?php

namespace App\Mail\Tenant;

use App\Models\ServiceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewServiceRecordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected ServiceRecord $serviceRecord,
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
        return $this->markdown('emails.tenant.new-service-record')->subject('New Service Record')->with([
            'text' => $this->serviceRecord->text,
            'url' => $this->url,
            'date' => Carbon::parse($this->serviceRecord->created_at)->toFormattedDateString(),
        ]);
    }
}
