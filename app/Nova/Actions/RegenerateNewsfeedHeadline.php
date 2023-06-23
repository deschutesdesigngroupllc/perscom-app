<?php

namespace App\Nova\Actions;

use App\Jobs\GenerateOpenAiNewsfeedContent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class RegenerateNewsfeedHeadline extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * @var string
     */
    public $name = 'Regenerate Newsfeed Headline';

    /**
     * @var string
     */
    public $confirmText = 'Are you sure you want to regenerate the headline?';

    /**
     * @var string
     */
    public $confirmButtonText = 'Regenerate Headline';

    /**
     * @var bool
     */
    public $showInline = true;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            GenerateOpenAiNewsfeedContent::dispatch($model, 'created', 'headline');
        }

        return Action::message('The newsfeed headline is being regenerated. The task will be executed in the background.');
    }
}
