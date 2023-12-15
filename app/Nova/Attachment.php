<?php

namespace App\Nova;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Attachment extends Resource
{
    public static string $model = \App\Models\Attachment::class;

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public static function createButtonLabel(): string
    {
        return 'Add Attachment';
    }

    public function subtitle(): ?string
    {
        return 'Resource: '.class_basename($this->model);
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Name')
                ->rules('required'),
            File::make('File', 'path')
                ->rules('required')
                ->storeOriginalName('filename')
                ->prunable(),
            MorphTo::make('Resource', 'model')
                ->types([
                    AssignmentRecord::class,
                    AwardRecord::class,
                    CombatRecord::class,
                    QualificationRecord::class,
                    RankRecord::class,
                    ServiceRecord::class,
                    Task::class,
                ]),
            URL::make('Download', function () {
                return Storage::temporaryUrl($this->path, now()->addMinute(), [
                    'disk' => 's3',
                ]);
            })
                ->onlyOnIndex(),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
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
        return [];
    }
}
