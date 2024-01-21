<?php

namespace App\Nova;

use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image as ImageField;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Image extends Resource
{
    public static string $model = \App\Models\Image::class;

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Name')
                ->rules('required'),
            Textarea::make('Description')
                ->alwaysShow()
                ->nullable(),
            Text::make('Description', function () {
                return Str::limit($this->description);
            })
                ->onlyOnIndex(),
            MorphTo::make('Resource', 'model')
                ->types([
                    Award::class,
                    Event::class,
                    PassportClient::class,
                    Qualification::class,
                    Rank::class,
                ]),
            URL::make('Image URL', function () {
                return $this->image_url;
            })
                ->displayUsing(function () {
                    return $this->image_url;
                }),
            ImageField::make('Image', 'path')
                ->rules(['required', File::image()->min('1kb')->max('10mb')])
                ->storeOriginalName('filename')
                ->disk('s3_public')
                ->deletable()
                ->prunable(),
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
