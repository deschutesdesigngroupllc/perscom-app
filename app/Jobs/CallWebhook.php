<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Spatie\WebhookServer\CallWebhookJob;

class CallWebhook extends CallWebhookJob implements ShouldBeUnique
{
}
