<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\TrainingRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

class NewTrainingRecordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected TrainingRecord $trainingRecord, protected string $url)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.new-training-record')
            ->subject('You have a new training record')
            ->with([
                'event' => $this->trainingRecord->event?->name,
                'instructor' => $this->trainingRecord->instructor?->name,
                'text' => $this->trainingRecord->text,
                'url' => $this->url,
                'date' => Date::parse($this->trainingRecord->created_at)->toFormattedDateString(),
            ]);
    }
}
