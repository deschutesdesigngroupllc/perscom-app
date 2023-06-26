<?php

namespace App\Nova;

use App\Models\Activity;
use App\Nova\Actions\RegenerateNewsfeedHeadline;
use App\Nova\Actions\RegenerateNewsfeedText;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Newsfeed extends Resource
{
    /**
     * @var string
     */
    public static $model = Activity::class;

    /**
     * @var string
     */
    public static $title = 'id';

    /**
     * @var string[]
     */
    public static $search = [
        'description', 'id',
    ];

    /**
     * @return string
     */
    public static function label()
    {
        return 'Newsfeed';
    }

    /**
     * @return string
     */
    public static function uriKey()
    {
        return 'newsfeed';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('log_name', 'newsfeed');
    }

    /**
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Text::make('Headline')
                ->resolveUsing(function () {
                    return $this->getExtraProperty('headline');
                })
                ->fillUsing(function (NovaRequest $request, Activity $activity, $attribute, $requestAttribute) {
                    $activity->properties = $activity->properties->put('headline', $request->input($requestAttribute));
                })
                ->rules('required'),
            Textarea::make('Text')
                ->resolveUsing(function () {
                    return $this->getExtraProperty('text');
                })
                ->fillUsing(function (NovaRequest $request, Activity $activity, $attribute, $requestAttribute) {
                    $activity->properties = $activity->properties->put('text', $request->input($requestAttribute));
                })
                ->rules('required')
                ->alwaysShow(),
            MorphTo::make('Subject'),
            DateTime::make('Date', 'created_at')->rules('required')->sortable(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [(new RegenerateNewsfeedHeadline()), (new RegenerateNewsfeedText())];
    }
}
