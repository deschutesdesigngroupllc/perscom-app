<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Traits\HasFields;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;

class Submission extends Resource
{
    use HasFields;
    use HasTabs;
    use HasActionsInTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Submission::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id'];

    /**
     * @var string[]
     */
    public static $orderBy = ['created_at' => 'desc'];

    /**
     * @return string
     */
    public function title()
    {
        return $this->id.optional($this->form, static function ($form) {
            return " - $form->name";
        });
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Form')->showOnPreview()->default(function (NovaRequest $request) {
                return $request->viaResource === Form::uriKey() ? $request->viaResourceId : null;
            }),
            BelongsTo::make('User')->showOnPreview()->default(function (NovaRequest $request) {
                return $request->user()->id;
            }),
            $this->generateBadgeField($this),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->exceptOnForms()->sortable(),
            DateTime::make('Updated At')->exceptOnForms()->sortable(),
            $this->getFields($request, true, function ($form) {
                return $form?->name;
            }, function ($model) {
                return $model?->form;
            }),
            Tabs::make('Relations', [
                Tab::make('Status History', [
                    MorphToMany::make('Status', 'statuses', Status::class)
                        ->allowDuplicateRelations()
                        ->fields(function () {
                            return [
                                Textarea::make('Text'),
                                Text::make('Text', function ($model) {
                                    return $model->text;
                                }),
                                DateTime::make('Updated At')->sortable()->onlyOnIndex(),
                            ];
                        }),
                ]),
                Tab::make('Logs', [$this->actionfield()]),
            ]),
        ];
    }

    /**
     * @return Badge
     */
    protected function generateBadgeField($submission)
    {
        $status = $submission->statuses()->first();

        $badge = Badge::make('Status', static function () use ($status) {
            return $status->name ?? 'No Current Status';
        })->types([
            'No Current Status' => 'bg-gray-100 text-gray-600',
        ])->label(function ($status) {
            return $status ?? 'No Current Status';
        })->showOnPreview();

        if ($status) {
            $badge->addTypes([
                $status->name => $status->color,
            ]);
        }

        return $badge;
    }

    /**
     * @return Panel
     */
    protected function getCustomFields(NovaRequest $request)
    {
        $form = $this->getForm($request);

        $fields = [];
        if ($form) {
            foreach ($form->fields as $field) {
                $fields[] = $field->constructNovaField();
            }
        }

        return new Panel($form->name ?? 'Form', $fields);
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
        return [ExportAsCsv::make('Export '.self::label())->canSee(function () {
            return Feature::active(ExportDataFeature::class);
        })->nameable()];
    }
}
