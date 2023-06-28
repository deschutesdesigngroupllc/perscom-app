<?php

namespace App\Nova;

use App\Nova\Actions\RegenerateNewsfeedHeadline;
use App\Nova\Actions\RegenerateNewsfeedText;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Newsfeed extends Resource
{
    /**
     * @var string
     */
    public static $model = \App\Models\Newsfeed::class;

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
     * @return string
     */
    public static function createButtonLabel()
    {
        return 'Create Newsfeed Item';
    }

    /**
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Hidden::make('Log Name', 'log_name')->default('newsfeed'),
            Hidden::make('Description', 'description')->default('created'),
            Hidden::make('Event', 'event')->default('created'),
            Text::make('Headline')
                ->resolveUsing(function ($value, $resource, $attribute) {
                    return $resource->id ? $resource->getExtraProperty('headline') : null;
                })
                ->fillUsing(function (NovaRequest $request, $activity, $attribute, $requestAttribute) {
                    $activity->properties = Collection::wrap($activity->properties)->put('headline', $request->input($requestAttribute));
                })
                ->rules('required'),
            Trix::make('Text')
                ->resolveUsing(function ($value, $resource, $attribute) {
                    return $resource->id ? $resource->getExtraProperty('text') : null;
                })
                ->fillUsing(function (NovaRequest $request, $activity, $attribute, $requestAttribute) {
                    $activity->properties = Collection::wrap($activity->properties)->put('text', $request->input($requestAttribute));
                })
                ->rules('required')
                ->alwaysShow(),
            DateTime::make('Date', 'created_at')
                ->default(now())
                ->rules('required')
                ->sortable(),
            Panel::make('Details', [
                MorphTo::make('Causer', 'causer')->types([
                    User::class,
                ])
                    ->help('Set the author of this newsfeed item.')
                    ->default(Auth::user()->getKey())
                    ->defaultResource(User::class),
                MorphTo::make('Subject', 'subject')->types([
                    Announcement::class,
                    AssignmentRecord::class,
                    AwardRecord::class,
                    CombatRecord::class,
                    QualificationRecord::class,
                    RankRecord::class,
                    ServiceRecord::class,
                ])->help('Set a resource that this newsfeed item is about.')->nullable(),
            ]),
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
