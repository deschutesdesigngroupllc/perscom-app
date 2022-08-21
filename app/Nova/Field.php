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
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\TagsField\Tags;

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
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        if (!request()->get('orderBy')) {
            return parent::applyOrderings($query, [
                'name' => 'asc',
            ]);
        }
        return parent::applyOrderings($query, $orderings);
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
            ID::make()->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules(['required']),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Text::make('Description', function () {
                return Str::limit($this->description);
            })->onlyOnIndex(),
            Tags::make('Tags')->withLinkToTagResource(),
            Select::make('Type')
                ->options(\App\Models\Field::$fieldTypes)
                ->sortable()
                ->displayUsingLabels(),
            Boolean::make('Required')->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                if ($formData->type === 'static') {
                    $field->hide();
                }
            }),
            Boolean::make('Readonly')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if (
                        $formData->type === 'radiogroup' ||
                        $formData->type === 'radio' ||
                        $formData->type === 'static' ||
                        $formData->type === 'checkbox' ||
                        $formData->type === 'select'
                    ) {
                        $field->hide();
                    }
                })
                ->help(
                    'A readonly input field cannot be modified (however, a user can tab to it, highlight it, and copy the text from it).'
                ),
            Boolean::make('Disabled')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === 'static') {
                        $field->hide();
                    }
                })
                ->help('A disabled input element is unusable and un-clickable.'),
            Text::make('Placeholder')
                ->hideFromIndex()
                ->hide()
                ->help('If a text type field, this text will fill the field when no value is present.')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if (
                        $formData->type === 'text' ||
                        $formData->type === 'textarea' ||
                        $formData->type === 'email' ||
                        $formData->type === 'password'
                    ) {
                        $field->show();
                    }
                }),
            Text::make('Help')
                ->hideFromIndex()
                ->help('Like this text, this is a short description that should help the user fill out the field.')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === 'static') {
                        $field->hide();
                    }
                }),
            Markdown::make('Text')
                ->hideFromIndex()
                ->hide()
                ->help('Like this text, this is a short description that should help the user fill out the field.')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === 'static' || $formData->type === 'radio') {
                        $field->show();
                    }
                }),
            KeyValue::make('Options')
                ->hide()
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === 'select' || $formData->type === 'radiogroup') {
                        $field->show();
                    }
                }),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
            MorphedByMany::make('Assigned Forms', 'forms', Form::class)->fields(function ($request, $relatedModel) {
                return [
                    Number::make('Order')
                        ->sortable()
                        ->rules('required'),
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
