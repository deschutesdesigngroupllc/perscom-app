<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\AwardRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewAwardRecordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected AwardRecord $awardRecord, protected string $url)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.new-award-record')
            ->subject('New Award Record')
            ->with([
                'award' => $this->awardRecord->award?->name,
                'text' => $this->awardRecord->text,
                'url' => $this->url,
                'date' => Carbon::parse($this->awardRecord->created_at)->toFormattedDateString(),
            ]);
    }
}
