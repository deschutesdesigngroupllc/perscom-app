<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use Emilianotisato\NovaTinyMCE\NovaTinyMCE;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;

class Announcement extends Resource
{
    public static string $model = \App\Models\Announcement::class;

    /**
     * @var string
     */
    public static $title = 'title';

    /**
     * @var array
     */
    public static $search = ['id', 'title'];

    public static function label(): string
    {
        return Str::plural(Str::title(setting('localization_announcements', 'Announcements')));
    }

    public static function uriKey(): string
    {
        return Str::plural(Str::slug(setting('localization_announcements', 'announcements')));
    }

    public function subtitle(): ?string
    {
        return "Created At: {$this->created_at->toDayDateTimeString()}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Title')
                ->rules('required')
                ->hideFromDetail()
                ->showOnPreview(),
            NovaTinyMCE::make('Content')
                ->rules('required')
                ->alwaysShow()
                ->hideFromDetail()
                ->showOnPreview(),
            Select::make('Color')
                ->displayUsingLabels()
                ->options([
                    'info' => 'Information (Blue)',
                    'success' => 'Success (Green)',
                    'warning' => 'Warning (Yellow)',
                    'failure' => 'Failure (Red)',
                ])
                ->rules('required')
                ->default(function () {
                    return 'info';
                })
                ->onlyOnForms(),
            DateTime::make('Expires At'),
            new Panel($this->title, [
                Trix::make('Details', 'content')
                    ->rules('required')
                    ->onlyOnDetail()
                    ->alwaysShow(),
            ]),
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
