<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\HtmlField\HtmlField;

class Document extends Resource
{
    public static string $model = \App\Models\Document::class;

    public static array $orderBy = ['name' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public static function label(): string
    {
        return Str::plural(Str::title(setting('localization_documents', 'Documents')));
    }

    public static function uriKey(): string
    {
        return Str::plural(Str::slug(setting('localization_documents', 'documents')));
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules(['required'])
                ->showOnPreview(),
            Tag::make('Tags')
                ->showCreateRelationButton()
                ->withPreview()
                ->showOnPreview(),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Trix::make('Content')
                ->hideFromIndex()
                ->help('Use the document tags below to dynamically inject content into your document when the document is attached to certain records.')
                ->rules(['required'])
                ->showOnPreview()
                ->withFiles('s3_public'),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
            new Panel('Document Tags', [
                HtmlField::make('Document Tags')
                    ->view('fields.html.document-tags')
                    ->onlyOnForms(),
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
        return [ExportAsCsv::make('Export Documents')
            ->canSee(function () {
                return Feature::active(ExportDataFeature::class);
            })
            ->nameable()];
    }
}
