<?php
/*
 * Copyright (c) 6/27/22, 9:06 PM Deschutes Design Group LLC.year. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Nova;

use App\Nova\Forms\Form;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Field extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Field::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'name'];

    /**
     * @return string
     */
    public static function label()
    {
        return 'Custom Fields';
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
            Text::make('Name')
                ->sortable()
                ->rules(['required']),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Select::make('Type')
                ->options(
                    collect(\App\Models\Field::fieldTypes)->mapWithKeys(function ($value, $key) {
                        return [$key => $key];
                    })
                )
                ->sortable()
                ->searchable()
                ->displayUsingLabels(),
            Boolean::make('Required')->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                if ($formData->type === 'Heading' || $formData->type === 'Line') {
                    $field->hide();
                }
            }),
            Text::make('Placeholder')
                ->hideFromIndex()
                ->help('If a text type field, this text will fill the field when no value is present.')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === 'Heading' || $formData->type === 'Line') {
                        $field->hide();
                    }
                }),
            Text::make('Help')
                ->hideFromIndex()
                ->help('Like this text, this is a short description that should help the user fill out the field.')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === 'Heading' || $formData->type === 'Line') {
                        $field->hide();
                    }
                }),
            KeyValue::make('Options')
                ->hide()
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === 'Select') {
                        $field->show();
                    }
                }),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
            MorphedByMany::make('Assigned Forms', 'forms', Form::class),
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
        return [];
    }
}
