<?php

namespace App\Nova\Forms;

use App\Models\Field as CustomField;
use App\Models\Forms\Form;
use App\Models\Forms\Submission as SubmissionModel;
use App\Nova\Lenses\CurrentUsersSubmissions;
use App\Nova\Resource;
use App\Nova\Status;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Submission extends Resource
{
    use HasTabs;
    use HasActionsInTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Forms\Submission::class;

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
     * @var array
     */
    protected $customFields = [];

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel()
    {
        return 'Submit Form';
    }

    /**
     * @param  null  $resource
     */
    public function __construct($resource = null)
    {
        parent::__construct($resource);

        // Get the fields we will be using for the submission
        $resourceId = Route::current()->parameter('resourceId');
        $submission = SubmissionModel::find($resourceId);
        $fields =
            $submission?->form->fields->sortBy(function ($field) {
                return $field->pivot->order;
            }) ?? CustomField::all();

        // Load all possible custom fields
        foreach ($fields as $field) {
            // Build our nova field
            $novaField = \call_user_func(
                ["Laravel\\Nova\\Fields\\{$field->type}", 'make'],
                $field->name,
                "field_{$field->id}"
            );
            if ($novaField instanceof Field) {
                // Placeholder
                if ($field->placeholder && method_exists($novaField, 'placeholder')) {
                    $novaField->placeholder($field->placeholder);
                }

                // Help
                if ($field->help && method_exists($novaField, 'help')) {
                    $novaField->help($field->help);
                }

                // Display properties
                $novaField->hideFromIndex();
                $novaField->showOnPreview();

                // Custom changes for specific fields
                if ($novaField instanceof Country) {
                    // Display as
                    $novaField->displayUsingLabels();
                }
                if ($novaField instanceof Select) {
                    // Display as
                    $novaField->options(collect($field->options)->toArray())->displayUsingLabels();
                }

                // Configure which fields are shown depending on the form
                $novaField->hide();
                $novaField->dependsOn(['form'], function ($resource, NovaRequest $request, FormData $formData) use (
                    $field,
                    $novaField
                ) {
                    $form = Form::find($formData->form);
                    if ($form && $form->fields->pluck('id')->search($field->id) !== false) {
                        $novaField->show();
                        if ($field->required && method_exists($novaField, 'required')) {
                            $novaField->rules('required');
                        }
                    }
                });

                // Save the fields to our array
                $this->customFields[] = $novaField;
            }
        }
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->hasPermissionTo('view:submission')) {
            return $query;
        }

        return $query->where('user_id', $request->user()->id);
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
            Hidden::make('User', 'user_id')
                ->default(function (NovaRequest $request) {
                    return $request->user()->id;
                })
                ->showOnDetail(),
            BelongsTo::make('Form')->showOnPreview(),
            BelongsTo::make('User')
                ->showOnPreview()
                ->canSee(function (NovaRequest $request) {
                    return $request->user()->hasPermissionTo('update:submission');
                }),
            Badge::make('Status', function () {
                return $this->status->name ?? 'none';
            })
                ->types([
                    'none' => 'bg-gray-100 text-gray-600',
                    $this->status?->name => $this->status?->color,
                ])
                ->label(function () {
                    return $this->status->name ?? 'No Current Status';
                }),
            new Panel('Form', $this->customFields),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->exceptOnForms(),
            DateTime::make('Updated At')->exceptOnForms(),
            Tabs::make('Relations', [
                Tab::make('Status History', [
                    MorphToMany::make('Status History', 'statuses', Status::class)->fields(function () {
                        return [
                            Textarea::make('Text'),
                            Text::make('Text', function ($model) {
                                return $model->text;
                            }),
                            DateTime::make('Updated At')
                                ->sortable()
                                ->onlyOnIndex(),
                        ];
                    }),
                ]),
                Tab::make('Logs', [$this->actionfield()]),
            ]),
        ];
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
        return [
            (new CurrentUsersSubmissions())->canSee(function (NovaRequest $request) {
                return $request->user()->hasPermissionTo('create:submission');
            }),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
