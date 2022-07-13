<?php

namespace App\Nova\Forms;

use App\Nova\Lenses\CurrentUsersSubmissions;
use App\Nova\Resource;
use App\Nova\Status;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

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
            new Panel('Form', $this->customFields),
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
