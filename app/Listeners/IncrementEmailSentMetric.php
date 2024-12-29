<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Metrics\EmailSentMetric;
use App\Metrics\Metric;
use Illuminate\Mail\Events\MessageSent;

class IncrementEmailSentMetric
{
    public function handle(MessageSent $event): void
    {
        Metric::increment(EmailSentMetric::class);
    }
}
