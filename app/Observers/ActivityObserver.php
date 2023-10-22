<?php

namespace App\Observers;

use App\Features\OpenAiGeneratedContent;
use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\Activity;
use Laravel\Pennant\Feature;

class ActivityObserver
{
    public function created(Activity $activity): void
    {
        if ($activity->log_name === 'newsfeed' && Feature::store('database')->active(OpenAiGeneratedContent::class)) {
            GenerateOpenAiNewsfeedContent::dispatch($activity, 'created', 'headline');
        }
    }
}
