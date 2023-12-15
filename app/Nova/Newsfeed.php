<?php

namespace App\Nova;

use App\Features\OpenAiGeneratedContent;
use App\Nova\Actions\RegenerateNewsfeedHeadline;
use App\Nova\Actions\RegenerateNewsfeedText;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;

class Newsfeed extends Resource
{
    public static string $model = \App\Models\Newsfeed::class;

    /**
     * @var string
     */
    public static $title = 'headline';

    /**
     * @var string[]
     */
    public static $search = ['description', 'id', 'properties'];

    public static function label(): string
    {
        return 'Newsfeed';
    }

    public static function uriKey(): string
    {
        return 'newsfeed';
    }

    public function subtitle(): ?string
    {
        return $this->resource->item;
    }

    public static function createButtonLabel(): string
    {
        return 'Create Newsfeed Item';
    }

    public static function updateButtonLabel(): string
    {
        return 'Update Newsfeed Item';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Hidden::make('Log Name', 'log_name')
                ->default('newsfeed'),
            Hidden::make('Description', 'description')
                ->default('created'),
            Hidden::make('Event', 'event')
                ->default('created'),
            Text::make('Headline')
                ->fillUsing(function (NovaRequest $request, $activity, $attribute, $requestAttribute) {
                    $activity->properties = Collection::wrap($activity->properties)
                        ->put('headline', $request->input($requestAttribute));
                })
                ->rules('required'),
            Trix::make('Text')
                ->fillUsing(function (NovaRequest $request, $activity, $attribute, $requestAttribute) {
                    $activity->properties = Collection::wrap($activity->properties)
                        ->put('text', $request->input($requestAttribute));
                })
                ->rules('required')
                ->alwaysShow(),
            DateTime::make('Date', 'created_at')
                ->default(now())
                ->rules('required')
                ->sortable(),
            Panel::make('Details', [
                MorphTo::make('Author', 'causer')
                    ->types([
                        User::class,
                    ])
                    ->help('Set the author of this newsfeed item.')
                    ->default(Auth::user()
                        ->getKey())
                    ->defaultResource(User::class),
                MorphTo::make('Subject', 'subject')
                    ->types([
                        Announcement::class,
                        AssignmentRecord::class,
                        AwardRecord::class,
                        CombatRecord::class,
                        QualificationRecord::class,
                        RankRecord::class,
                        ServiceRecord::class,
                    ])
                    ->help('Set a resource that this newsfeed item is about.')
                    ->nullable(),
            ]),
        ];
    }

    public static function authorizedToViewAny(Request $request): bool
    {
        return Gate::check('newsfeed', Auth::user());
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new RegenerateNewsfeedHeadline())->canSee(function () {
                return Feature::driver('database')
                    ->active(OpenAiGeneratedContent::class);
            }),
            (new RegenerateNewsfeedText())->canSee(function () {
                return Feature::driver('database')
                    ->active(OpenAiGeneratedContent::class);
            }),
        ];
    }
}
