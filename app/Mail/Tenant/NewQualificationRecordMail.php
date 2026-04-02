<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\QualificationRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

class NewQualificationRecordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected QualificationRecord $qualificationRecord, protected string $url)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.new-qualification-record')
            ->subject('You have a new qualification record')
            ->with([
                'qualification' => $this->qualificationRecord->qualification?->name,
                'text' => $this->qualificationRecord->text,
                'url' => $this->url,
                'date' => Date::parse($this->qualificationRecord->created_at)->toFormattedDateString(),
            ]);
    }
}
