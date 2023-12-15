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
use Laravel\Nova\Fields\BelongsTo;
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
    use HasActionsInTabs;
    use HasTabs;

    public static string $model = \App\Models\Form::class;

    public static array $orderBy = ['name' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules(['required'])
                ->showOnPreview(),
            Slug::make('Slug')
                ->from('Name')
                ->rules(['required', Rule::unique('forms', 'slug')
                    ->ignore($this->id)])
                ->help('The slug will be used in the URL to access the form.')
                ->canSee(function (NovaRequest $request) {
                    return Gate::check('update', $request->findModel());
                }),
            Text::make('Description', function () {
                return Str::limit($this->description);
            })
                ->onlyOnIndex(),
            Tag::make('Tags')
                ->showCreateRelationButton()
                ->withPreview(),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Markdown::make('Instructions'),
            new Panel('Submission', [
                Textarea::make('Success Message')
                    ->help('The message displayed when the form is successfully submitted.')
                    ->alwaysShow(),
                BelongsTo::make('Default Submission Status', 'submission_status', Status::class)
                    ->nullable()
                    ->hideFromIndex(),
            ]),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
            Tabs::make('Settings', [
                Tab::make('Fields', [
                    MorphToMany::make('Fields', 'fields', Field::class)
                        ->showCreateRelationButton(),
                ]),
                Tab::make('Submissions', [HasMany::make('Submissions', 'submissions', Submission::class)]),
                Tab::make('Notifications', [MorphToMany::make('Notifications', 'notifications', User::class)]),
                Tab::make('Logs', [$this->actionfield()]),
            ])
                ->showTitle(),
        ];
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
            ExportAsCsv::make('Export '.self::label())
                ->canSee(function () {
                    return Feature::active(ExportDataFeature::class);
                })
                ->nameable(),
            (new OpenForm())->showInline()
                ->canRun(function (NovaRequest $request) {
                    return Gate::check('view', $request->findModel());
                })
                ->canSee(function (NovaRequest $request) {
                    return Gate::check('view', $request->findModel());
                }),

        ];
    }
}
