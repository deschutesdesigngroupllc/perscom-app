<?php

namespace App\Nova\Forms;

use App\Models\Forms\Submission as SubmissionModel;
use App\Models\Forms\Form;
use App\Models\Field as CustomField;
use App\Nova\Lenses\CurrentUsersSubmissions;
use App\Nova\Resource;
use App\Nova\Status;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use ThinkStudio\HtmlField\Html;

class Submission extends Resource
{
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
		return 'New Form Submission';
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
		$fields = $submission?->form->fields->sortBy(function ($field) {
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
                // Required
                if ($field->required && method_exists($novaField, 'required')) {
                    $novaField->required();
                }

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
		            $novaField
			            ->options(collect($field->options)->toArray())
			            ->displayUsingLabels();
	            }

                // Configure which fields are shown depending on the form
                $novaField->hide();
                $novaField->dependsOn(['form'], function ($resource, NovaRequest $request, FormData $formData) use ($field, $novaField) {
	                $form = Form::find($formData->form);
                	if ($form && $form->fields->pluck('id')->search($field->id) !== false) {
                		$novaField->show();
	                }
                });

                // Save the fields to our array
                $this->customFields[] = $novaField;
            }
        }
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
            BelongsTo::make('User')->showOnPreview(),
            BelongsTo::make('Form')->showOnPreview(),
            new Panel('Form', $this->customFields),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->exceptOnForms(),
            DateTime::make('Updated At')->exceptOnForms(),
	        Badge::make('Status', function ($model) {
		        return $this->status->name ?? null;
	        })->map([
		        $this->status->name ?? null => 'info',
	        ]),
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
        return [new CurrentUsersSubmissions()];
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
