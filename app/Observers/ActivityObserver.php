<?php

namespace App\Observers;

use App\Features\OpenAiGeneratedContent;
use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\Activity;
use Laravel\Pennant\Feature;

class ActivityObserver
{
    /**
     * Handle the NewsfeedItem "created" event.
     */
    public function created(Activity $activity): void
    {
        if ($activity->log_name === 'newsfeed' && Feature::driver('database')->active(OpenAiGeneratedContent::class)) {
            GenerateOpenAiNewsfeedContent::dispatch($activity, 'created', 'headline');
        }
    }

    /**
     * Handle the NewsfeedItem "updated" event.
     */
    public function updated(Activity $activity): void
    {
        //
    }

    /**
     * Handle the NewsfeedItem "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        //
    }

    /**
     * Handle the NewsfeedItem "restored" event.
     */
    public function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the NewsfeedItem "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        //
    }
}
