<?php

namespace App\Observers;

use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\Activity;

class ActivityObserver
{
    /**
     * Handle the Newsfeed "created" event.
     */
    public function created(Activity $activity): void
    {
        if ($activity->log_name === 'newsfeed') {
            GenerateOpenAiNewsfeedContent::dispatch($activity, 'created', 'headline');
            GenerateOpenAiNewsfeedContent::dispatch($activity, 'created', 'text');
        }
    }

    /**
     * Handle the Newsfeed "updated" event.
     */
    public function updated(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Newsfeed "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Newsfeed "restored" event.
     */
    public function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Newsfeed "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        //
    }
}
