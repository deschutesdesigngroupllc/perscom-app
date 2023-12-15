<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Nova\Fields\TaskAssignmentFields;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;

class Task extends Resource
{
    public static string $model = \App\Models\Task::class;

    /**
     * @var string
     */
    public static $title = 'title';

    /**
     * @var array
     */
    public static $search = ['id', 'title'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Title')
                ->rules('required')
                ->showOnPreview(),
            Textarea::make('Description')
                ->alwaysShow()
                ->showOnPreview(),
            Text::make('Description', function () {
                return Str::limit($this->description);
            })
                ->onlyOnIndex(),
            Markdown::make('Instructions')
                ->nullable()
                ->help('Set to add some instructions to the task.'),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->sortable()
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->sortable()
                ->onlyOnDetail(),
            new Panel('Details', [
                BelongsTo::make('Form')
                    ->nullable()
                    ->help('Set to assign a form that needs to be completed as apart of the task.')
                    ->hideFromIndex(),
            ]),
            BelongsToMany::make('Assigned To', 'users', User::class)
                ->fields(new TaskAssignmentFields())
                ->referToPivotAs('assignment'),
            MorphMany::make('Attachments'),
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
