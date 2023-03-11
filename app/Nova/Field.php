<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Pennant\Feature;
use Outl1ne\NovaSortable\Traits\HasSortableManyToManyRows;

class Field extends Resource
{
    use HasSortableManyToManyRows;

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'name'];

    /**
     * @var int
     */
    public static $perPageViaRelationship = 10;

    /**
     * @var string[]
     */
    public static $orderBy = ['name' => 'asc'];

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return 'Type: '.Str::ucfirst($this->type);
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
            Text::make('Name')->sortable()->rules(['required']),
            Slug::make('Slug', 'key')->from('Name')->rules([
                'required',
                Rule::unique('fields', 'key')->ignore($this->id),
                'regex:/^[0-9a-zA-Z_]+$/',
            ])->separator('_')->help('The slug will be used as the field key when saving the form submission. Allowed characters: 0-9, a-z, A-Z, or underscore.'),
            Textarea::make('Description')->nullable()->alwaysShow()->showOnPreview(),
            Text::make('Description', function () {
                return Str::limit($this->description);
            })->onlyOnIndex(),
            Select::make('Type')->rules('required')->options(\App\Models\Field::$fieldTypes)->sortable()->displayUsingLabels(),
            Boolean::make('Required'),
            Boolean::make('Readonly')->help('A readonly input field cannot be modified (however, a user can tab to it, highlight it, and copy the text from it).'),
            Text::make('Placeholder')
                ->hideFromIndex()
                ->hide()
                ->help('If a text type field, this text will fill the field when no value is present.')
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === \App\Models\Field::FIELD_TEXT ||
                        $formData->type === \App\Models\Field::FIELD_TEXTAREA ||
                        $formData->type === \App\Models\Field::FIELD_EMAIL ||
                        $formData->type === \App\Models\Field::FIELD_PASSWORD) {
                        $field->show();
                    }
                }),
            Text::make('Help')
                ->hideFromIndex()
                ->help('Like this text, this is a short description that should help the user fill out the field.'),
            KeyValue::make('Options')
                    ->hide()
                    ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                        if ($formData->type === \App\Models\Field::FIELD_SELECT) {
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
        return [ExportAsCsv::make('Export '.self::label())->canSee(function () {
            return Feature::active(ExportDataFeature::class);
        })->nameable()];
    }
}
