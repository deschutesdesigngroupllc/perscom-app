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
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Outl1ne\NovaSortable\Traits\HasSortableManyToManyRows;

class Field extends Resource
{
    use HasSortableManyToManyRows;

    public static string $model = \App\Models\Field::class;

    public static array $orderBy = ['name' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    /**
     * @var int
     */
    public static $perPageViaRelationship = 10;

    public function subtitle(): ?string
    {
        return 'Type: '.Str::ucfirst($this->type);
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules(['required']),
            Slug::make('Slug', 'key')
                ->from('Name')
                ->rules([
                    'required',
                    Rule::unique('fields', 'key')
                        ->ignore($this->id),
                    'regex:/^[0-9a-zA-Z_]+$/',
                ])
                ->hideFromIndex()
                ->separator('_')
                ->help('The slug will be used as the field key when saving the form submission. Allowed characters: 0-9, a-z, A-Z, or underscore.'),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Text::make('Description', function () {
                return Str::limit($this->description);
            })
                ->onlyOnIndex(),
            Select::make('Type')
                ->rules('required')
                ->options(\App\Models\Field::$fieldTypes)
                ->sortable()
                ->displayUsingLabels(),
            Boolean::make('Readonly')
                ->help('A readonly input field cannot be modified (however, a user can tab to it, highlight it, and copy the text from it).'),
            KeyValue::make('Options')
                ->hide()
                ->dependsOn('type', function ($field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === \App\Models\Field::FIELD_SELECT) {
                        $field->show();
                    }
                }),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
            Panel::make('Details', [
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
            ]),
            Panel::make('Validation', [
                Boolean::make('Required'),
                Text::make('Rules')
                    ->hideFromIndex()
                    ->nullable()
                    ->help('A pipe delimited list of validation rules that can be found <a target="_blank" href="https://laravel.com/docs/10.x/validation#available-validation-rules">here</a>.'),
            ]),
            Panel::make('Visibility', [
                Boolean::make('Hidden')
                    ->help('The field will only be shown if the user has editable permissions.'),
            ]),
            MorphedByMany::make('Assigned Forms', 'forms', Form::class),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [ExportAsCsv::make('Export '.self::label())
            ->canSee(function () {
                return Feature::active(ExportDataFeature::class);
            })
            ->nameable()];
    }
}
