<?php

namespace App\Nova;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Mail extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Mail>
     */
    public static $model = \App\Models\Mail::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'subject';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'subject',
        'content',
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Mail';
    }

    /**
     * @return string
     */
    public static function uriKey()
    {
        return 'mail';
    }

    /**
     * @return string
     */
    public static function createButtonLabel()
    {
        return 'Send Mail';
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "Sent At: {$this->sent_at->toDayDateTimeString()}";
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
            Text::make('Subject')->rules('required')->sortable(),
            Markdown::make('Content')->rules('required'),
            MultiSelect::make('Recipients')->options(function () {
                if (Request::isCentralRequest()) {
                    return Tenant::all()->pluck('name', 'id')->sort();
                }

                return User::all()->pluck('name', 'id')->sort();
            })->hideFromIndex()->rules('required'),
            Boolean::make('Send Now', 'send_now')->default(true)->sortable(),
            DateTime::make('Send At', 'send_at')
                ->hide()
                ->dependsOn(['send_now'], function (DateTime $field, NovaRequest $request, $formData) {
                    if ($formData->send_now === false) {
                        $field->rules('required')->show();
                    }
                }),
            DateTime::make('Sent At', 'sent_at')->exceptOnForms(),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->exceptOnForms()->sortable(),
            DateTime::make('Updated At')->onlyOnDetail(),
            new Panel('Links', [
                KeyValue::make('Links', 'links')->rules('json')->keyLabel('Text')->valueLabel('URL'),
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
        return [];
    }
}
