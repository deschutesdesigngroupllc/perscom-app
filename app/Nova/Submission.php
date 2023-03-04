<?php

namespace App\Nova;

use App\Facades\Feature;
use App\Models\Enums\FeatureIdentifier;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Perscom\HtmlField\HtmlField;

class Submission extends Resource
{
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
     * @return string
     */
    public static function createButtonLabel()
    {
        return 'Submit Form';
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        $novaRequest = NovaRequest::createFrom($request);

        return $novaRequest->viaResource && $novaRequest->viaResourceId && parent::authorizedToCreate($request);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            $this->getDetailFields($request),
            $this->getInstructionFields($request),
            $this->generateBadgeField($this),
            Code::make('Data', static function ($model) {
                return json_encode($model->getAttributes(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            })->hideFromIndex()->json()->canSeeWhen('update', $request->model()),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->exceptOnForms()->sortable(),
            DateTime::make('Updated At')->exceptOnForms()->sortable(),
            $this->getCustomFields($request),
            Tabs::make('Relations', [
                Tab::make('Status History', [
                    MorphToMany::make('Status', 'statuses', Status::class)->allowDuplicateRelations()->fields(
                        function () {
                            return [
                                Textarea::make('Text'),
                                Text::make('Text', function ($model) {
                                    return $model->text;
                                }),
                                DateTime::make('Updated At')->sortable()->onlyOnIndex(),
                            ];
                        }
                    ),
                ]),
                Tab::make('Logs', [$this->actionfield()]),
            ]),
        ];
    }

    /**
     * @param $submission
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
        });

        if ($status) {
            $badge->addTypes([
                $status->name => $status->color,
            ]);
        }

        return $badge;
    }

    /**
     * @param  NovaRequest  $request
     * @return Panel
     */
    protected function getDetailFields(NovaRequest $request)
    {
        $form = $this->getForm($request);

        if (Gate::check('update', $request->model())) {
            return new Panel('Details', [
                BelongsTo::make('Form')->showOnPreview()->default(function (
                    NovaRequest $request
                ) {
                    return $request->viaResource === Form::uriKey() ? $request->viaResourceId : null;
                }),
                BelongsTo::make('User')->showOnPreview()->default(function (
                    NovaRequest $request
                ) {
                    return $request->user()->id;
                })->help('The user will be set to guest if left blank.'),
            ]);
        }

        $fields = [];

        if ($request->isFormRequest()) {
            $fields[] = Hidden::make('User', 'user_id')->default(function (
                NovaRequest $request
            ) {
                return $request->user()->id;
            })->showOnDetail();
            $fields[] = Hidden::make('Form', 'form_id')->default(function (
                NovaRequest $request
            ) {
                return $request->viaResource === Form::uriKey() ? $request->viaResourceId : null;
            })->showOnDetail();
        }

        return new Panel(
            $form->name ?? 'Form', array_merge($fields, [
                Text::make('User', static function ($submission) {
                    return optional($submission->user, static function ($user) {
                        return $user->name;
                    }) ?? 'Guest';
                })->onlyOnIndex(),
                Text::make('Form', static function ($submission) {
                    return optional($submission->form, static function ($form) {
                        return $form->name;
                    }) ?? 'Form';
                })->onlyOnIndex(),
            ])
        );
    }

    /**
     * @param  NovaRequest  $request
     * @return mixed|null
     */
    protected function getForm(NovaRequest $request)
    {
        $form = null;

        if ($request->resource() === __CLASS__) {
            if (($resourceId = $request->viaResourceId) && $request->isCreateOrAttachRequest()) {
                $form = \App\Models\Form::findOrFail($resourceId);
            }

            if (($submission = $request->findModel()) &&
                ($request->isUpdateOrUpdateAttachedRequest() || $request->isPresentationRequest())) {
                return $submission->form;
            }
        }

        return $form;
    }

    /**
     * @param  NovaRequest  $request
     * @return Panel
     */
    protected function getInstructionFields(NovaRequest $request)
    {
        $form = $this->getForm($request);

        return new Panel('Instructions', [
            HtmlField::make('Instructions')->view('fields.html.form-instructions', [
                'instructions' => Str::markdown($form->instructions ?? ''),
            ])->canSee(function () use ($form) {
                return isset($form->instructions) && $form->instructions !== '';
            })->onlyOnForms(),
        ]);
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
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            ExportAsCsv::make('Export '.self::label())->canSee(function () {
                return Feature::isAccessible(FeatureIdentifier::FEATURE_EXPORT_DATA);
            })->nameable(),
        ];
    }
}
