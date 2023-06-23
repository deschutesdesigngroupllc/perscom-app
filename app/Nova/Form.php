<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Nova\Actions\OpenForm;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;

class Form extends Resource
{
    use HasTabs;
    use HasActionsInTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Form::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'name'];

    /**
     * @var string[]
     */
    public static $orderBy = ['name' => 'asc'];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->hideFromIndex(),
            Text::make('Name')->sortable()->rules(['required'])->showOnPreview(),
            Slug::make('Slug')
                ->from('Name')
                ->rules(['required', Rule::unique('forms', 'slug')->ignore($this->id)])
                ->help('The slug will be used in the URL to access the form.')
                ->canSee(function (NovaRequest $request) {
                    return Gate::check('update', $request->findModel());
                }),
            Text::make('Description', function () {
                return Str::limit($this->description);
            })->onlyOnIndex(),
            Tag::make('Tags')->showCreateRelationButton()->withPreview(),
            Textarea::make('Description')->nullable()->alwaysShow()->showOnPreview(),
            Markdown::make('Instructions'),
            new Panel('Submission', [
                Textarea::make('Success Message')
                    ->help('The message displayed when the form is successfully submitted.')
                    ->alwaysShow(),
            ]),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
            Tabs::make('Relations', [
                Tab::make('Fields', [MorphToMany::make('Fields', 'fields', Field::class)]),
                Tab::make('Submissions', [HasMany::make('Submissions', 'submissions', Submission::class)]),
                Tab::make('Logs', [$this->actionfield()]),
            ]),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            ExportAsCsv::make('Export '.self::label())->canSee(function () {
                return Feature::active(ExportDataFeature::class);
            })->nameable(),
            (new OpenForm())->showInline()->canRun(function (NovaRequest $request) {
                return Gate::check('view', $request->findModel());
            })->canSee(function (NovaRequest $request) {
                return Gate::check('view', $request->findModel());
            }),

        ];
    }
}
