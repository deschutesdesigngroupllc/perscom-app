<?php

namespace App\Nova\Actions;

use App\Jobs\GenerateOpenAiNewsfeedContent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class RegenerateNewsfeedText extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * @var string
     */
    public $name = 'Regenerate Newsfeed Item Text';

    /**
     * @var string
     */
    public $confirmText = 'Are you sure you want to regenerate the text?';

    /**
     * @var string
     */
    public $confirmButtonText = 'Regenerate Text';

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
            GenerateOpenAiNewsfeedContent::dispatch($model, 'created', 'text');
        }

        return Action::message('The newsfeed text is being regenerated. The task will be executed in the background.');
    }
}
