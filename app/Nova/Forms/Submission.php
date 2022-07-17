<?php

namespace App\Nova\Forms;

use App\Nova\Lenses\CurrentUsersSubmissions;
use App\Nova\Resource;
use App\Nova\Status;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
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

    protected $customFields = [];

    public function __construct($resource = null)
    {
        parent::__construct($resource);

        $fields = [
            [
                'field' => 'Text',
                'name' => 'Test',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Textarea',
                'name' => 'Test 2',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Datetime',
                'name' => 'Test 3',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Date',
                'name' => 'Test 7',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Email',
                'name' => 'Test 4',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Heading',
                'name' => 'Heading 1',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Boolean',
                'name' => 'Test 5',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Country',
                'name' => 'Test 6',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'KeyValue',
                'name' => 'Test 7',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Line',
                'name' => 'Test 8',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Markdown',
                'name' => 'Test 9',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Number',
                'name' => 'Test 9',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
            [
                'field' => 'Password',
                'name' => 'Test 9',
                'required' => true,
                'placeholder' => 'This is some placeholder text.',
                'help' => 'This is some help text.',
            ],
        ];

        foreach ($fields as $field) {
            $novaField = \call_user_func(["Laravel\\Nova\\Fields\\{$field['field']}", 'make'], $field['name'], 'data');
            if ($field['required'] && method_exists($novaField, 'required')) {
                $novaField->required();
            }
            if ($field['placeholder'] && method_exists($novaField, 'placeholder')) {
                $novaField->placeholder($field['placeholder']);
            }
            if ($field['help'] && method_exists($novaField, 'help')) {
                $novaField->help($field['help']);
            }
            $this->customFields[] = $novaField;
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
	        Html::make('Instructions', function () {
	        	return view('fields.test')->render();
	        })->showOnCreating(),
	        Textarea::make('Description')
                ->hide()
                ->alwaysShow()
                ->readonly()
                ->dependsOn('form', function (Textarea $field, NovaRequest $request, FormData $formData) {
                    if ($formId = $formData->form) {
                        $form = \App\Models\Forms\Form::find($formId);
                        $field->fillUsing(function ($request, $model, $attribute, $requestAttribute) use ($form) {
	                        $model->{$attribute} = $form->description;
                        })->show();
                    }
                }),
            Badge::make('Status', function ($model) {
                return $this->status->name ?? null;
            })
                ->map([
                    $this->status->name ?? null => 'info',
                ])
                ->showOnPreview(),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->exceptOnForms(),
            DateTime::make('Updated At')->onlyOnDetail(),
            MorphToMany::make('Status History', 'statuses', Status::class)->fields(function () {
                return [
                    Textarea::make('Text'),
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
